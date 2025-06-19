<?php

namespace WechatWorkMsgAuditBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use MoChat\WeWorkFinanceSDK\Provider\FFIProvider;
use MoChat\WeWorkFinanceSDK\WxFinanceSDK;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarExporter\VarExporter;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkBundle\Enum\SpecialAgent;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

/**
 * @see https://developer.work.weixin.qq.com/document/path/91774
 */
#[AsCronTask('* * * * *')]
#[AsCommand(name: self::NAME, description: '同步归档消息')]
class SyncArchiveMessageCommand extends Command
{
    public const NAME = 'wechat-work:sync-archive-message';

    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly FilesystemOperator $mountManager,
        private readonly ArchiveMessageRepository $messageRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('corpId', InputArgument::OPTIONAL, '企业ID');
        $this->addArgument('agentId', InputArgument::OPTIONAL, '应用ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $corpIdArg = $input->getArgument('corpId');
        if (!empty($corpIdArg)) {
            $corp = $this->corpRepository->findOneBy(['corpId' => $corpIdArg]);
            if (null === $corp) {
                $output->writeln('找不到企业');

                return Command::FAILURE;
            }

            $corps = [$corp];
        } else {
            $corps = $this->corpRepository->findAll();
        }

        foreach ($corps as $corp) {
            $agent = null;
            $agentIdArg = $input->getArgument('agentId');
        if (!empty($agentIdArg)) {
                $agent = $this->agentRepository->findOneBy([
                    'corp' => $corp,
                    'agentId' => $agentIdArg,
                ]);
            } else {
                foreach ($corp->getAgents() as $item) {
                    if ($item->getAgentId() === SpecialAgent::MESSAGE_ARCHIVE->value) {
                        $agent = $item;
                        break;
                    }
                }
            }

            if (null === $agent) {
                $output->writeln("找不到[{$corp->getName()}]的消息归档应用");
                continue;
            }

            $output->writeln("正在归档[{$corp->getName()}]的消息");
            if (empty($agent->getPrivateKeyContent())) {
                $output->writeln("[{$corp->getName()}]未配置秘钥信息，不能同步");
                continue;
            }

            // # 企业配置
            $corpConfig = [
                'corpid' => $corp->getCorpId(),
                'secret' => $agent->getSecret(),
                'private_keys' => [
                    $agent->getPrivateKeyVersion() => trim($agent->getPrivateKeyContent()),
                ],
            ];

            // 包配置
            $srcConfig = [
                'default' => 'php-ffi',
                'providers' => [
                    'php-ffi' => [
                        'driver' => FFIProvider::class,
                    ],
                ],
            ];

            // 1、实例化
            $sdk = WxFinanceSDK::init($corpConfig, $srcConfig);

            $lastMessage = $this->messageRepository->findOneBy(['corp' => $corp], ['id' => 'DESC']);
            $seq = $lastMessage !== null ? $lastMessage->getSeq() - 1 : 0;

            // 获取聊天记录
            $chatData = $sdk->getDecryptChatData($seq, 200);
            $output->writeln(VarExporter::export($chatData));

            // see https://developers.weixin.qq.com/community/develop/doc/0002ce370ec37812b02a12d4556000
            $map = [
                'image' => 'png',
                'voice' => 'amr',
                'video' => 'mp4',
            ];

            foreach ($chatData as $datum) {
                $message = $this->messageRepository->findOneBy([
                    'corp' => $corp,
                    'msgId' => $datum['msgid'],
                ]);
                if (null !== $message) {
                    // 处理过了，我们跳过
                    continue;
                }

                $message = new ArchiveMessage();
                $message->setContext($datum);
                $message->setCorp($corp);
                $message->setMsgId($datum['msgid']);
                $message->setAction($datum['action']);

                // switch是很特殊的。。格式如 [
                //    'msgid' => '16666922367251035000_1668250656103',
                //    'action' => 'switch',
                //    'time' => 1668250655937,
                //    'user' => 'felix_ye',
                //    'seq' => 16,
                // ]
                if ('switch' === $message->getAction()) {
                    $message->setFromUserId($datum['user']);
                    $message->setMsgTime(CarbonImmutable::createFromTimestampMs($datum['time'])->toDateTimeImmutable());
                } else {
                    $message->setFromUserId($datum['from']);
                    $message->setToList($datum['tolist']);
                    $message->setRoomId($datum['roomid']);
                    $message->setMsgTime(CarbonImmutable::createFromTimestampMs($datum['msgtime'])->toDateTimeImmutable());
                }

                $message->setSeq($datum['seq']);

                if (isset($datum['msgtype'])) {
                    $message->setMsgType($datum['msgtype']);
                    $message->setContent($datum[$message->getMsgType()]);

                    // 如果content里面有 sdkfileid ，那就说明有媒体？
                    $content = $message->getContent();
                    if (isset($content['sdkfileid'])) {
                        $ext = $map[$message->getMsgType()] ?? 'raw';
                        $file = $sdk->getMediaData($message->getContent()['sdkfileid'], $ext);
                        $destinationPath = 'archive/' . uniqid() . '.' . $ext;
                        $this->mountManager->write($destinationPath, $file);
                        $key = $destinationPath;
                        $content['fileKey'] = $key;
                        $message->setContent($content);
                    }
                }

                $this->entityManager->persist($message);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
