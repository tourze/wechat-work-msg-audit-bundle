<?php

declare(strict_types=1);

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
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\VarExporter\VarExporter;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Enum\SpecialAgent;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

/**
 * @see https://developer.work.weixin.qq.com/document/path/91774
 */
#[Autoconfigure(public: true)]
#[AsCronTask(expression: '* * * * *')]
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
        $corps = $this->getCorps($input, $output);
        if (null === $corps) {
            return Command::FAILURE;
        }

        foreach ($corps as $corp) {
            $this->processCorp($corp, $input, $output);
        }

        return Command::SUCCESS;
    }

    /**
     * @return Corp[]|null
     */
    private function getCorps(InputInterface $input, OutputInterface $output): ?array
    {
        $corpIdArg = $input->getArgument('corpId');
        if (null !== $corpIdArg && '' !== $corpIdArg) {
            $corp = $this->corpRepository->findOneBy(['corpId' => $corpIdArg]);
            if (null === $corp) {
                $output->writeln('找不到企业');

                return null;
            }

            return [$corp];
        }

        return $this->corpRepository->findAll();
    }

    private function processCorp(Corp $corp, InputInterface $input, OutputInterface $output): void
    {
        $agent = $this->getAgent($corp, $input);
        if (null === $agent) {
            $output->writeln("找不到[{$corp->getName()}]的消息归档应用");

            return;
        }

        $output->writeln("正在归档[{$corp->getName()}]的消息");
        if (null === $agent->getPrivateKeyContent() || '' === $agent->getPrivateKeyContent()) {
            $output->writeln("[{$corp->getName()}]未配置秘钥信息，不能同步");

            return;
        }

        $sdk = $this->createSdk($corp, $agent);
        $this->syncMessages($corp, $sdk, $output);
    }

    private function getAgent(Corp $corp, InputInterface $input): ?Agent
    {
        $agentIdArg = $input->getArgument('agentId');
        if (null !== $agentIdArg && '' !== $agentIdArg) {
            return $this->agentRepository->findOneBy([
                'corp' => $corp,
                'agentId' => $agentIdArg,
            ]);
        }

        foreach ($corp->getAgents() as $item) {
            if ($item->getAgentId() === SpecialAgent::MESSAGE_ARCHIVE->value) {
                return $item;
            }
        }

        return null;
    }

    private function createSdk(Corp $corp, Agent $agent): WxFinanceSDK
    {
        $corpConfig = [
            'corpid' => $corp->getCorpId(),
            'secret' => $agent->getSecret(),
            'private_keys' => [
                $agent->getPrivateKeyVersion() => trim((string) $agent->getPrivateKeyContent()),
            ],
        ];

        $srcConfig = [
            'default' => 'php-ffi',
            'providers' => [
                'php-ffi' => [
                    'driver' => FFIProvider::class,
                ],
            ],
        ];

        return WxFinanceSDK::init($corpConfig, $srcConfig);
    }

    private function syncMessages(Corp $corp, WxFinanceSDK $sdk, OutputInterface $output): void
    {
        /** @var ArchiveMessage|null $lastMessage */
        $lastMessage = $this->messageRepository->findOneBy(['corp' => $corp], ['id' => 'DESC']);
        $seq = null !== $lastMessage ? ((int) $lastMessage->getSeq()) - 1 : 0;

        $chatData = $sdk->getDecryptChatData($seq, 200);
        $output->writeln(VarExporter::export($chatData));

        $extensionMap = $this->getMediaExtensionMap();

        foreach ($chatData as $datum) {
            $this->processMessage($corp, $datum, $sdk, $extensionMap);
        }
    }

    /**
     * @return array<string, string>
     */
    private function getMediaExtensionMap(): array
    {
        return [
            'image' => 'png',
            'voice' => 'amr',
            'video' => 'mp4',
        ];
    }

    /**
     * @param array<string, mixed> $datum
     * @param array<string, string> $extensionMap
     */
    private function processMessage(Corp $corp, array $datum, WxFinanceSDK $sdk, array $extensionMap): void
    {
        $msgId = $this->ensureString($datum['msgid'] ?? '');
        if ($this->messageExists($corp, $msgId)) {
            return;
        }

        $message = $this->createMessage($corp, $datum);
        $this->handleMediaContent($message, $sdk, $extensionMap);

        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    private function messageExists(Corp $corp, string $msgId): bool
    {
        return null !== $this->messageRepository->findOneBy([
            'corp' => $corp,
            'msgId' => $msgId,
        ]);
    }

    /**
     * @param array<string, mixed> $datum
     */
    private function createMessage(Corp $corp, array $datum): ArchiveMessage
    {
        $message = new ArchiveMessage();
        $message->setContext($datum);
        $message->setCorp($corp);

        $message->setMsgId($this->ensureString($datum['msgid'] ?? ''));
        $message->setAction($this->ensureString($datum['action'] ?? ''));
        $message->setSeq($this->ensureInt($datum['seq'] ?? 0));

        $action = $message->getAction();
        if ('switch' === $action) {
            $this->setSwitchMessage($message, $datum);
        } else {
            $this->setChatMessage($message, $datum);
        }

        $this->setMessageContent($message, $datum);

        return $message;
    }

    /**
     * @param array<string, string> $extensionMap
     */
    private function handleMediaContent(ArchiveMessage $message, WxFinanceSDK $sdk, array $extensionMap): void
    {
        $content = $message->getContent();
        if (!isset($content['sdkfileid'])) {
            return;
        }

        $ext = $extensionMap[$message->getMsgType()] ?? 'raw';
        $file = $sdk->getMediaData($this->ensureString($content['sdkfileid'] ?? ''), $ext);
        $destinationPath = 'archive/' . uniqid() . '.' . $ext;
        $fileContent = file_get_contents($file->getPathname());
        if (false === $fileContent) {
            return;
        }
        $this->mountManager->write($destinationPath, $fileContent);

        $content['fileKey'] = $destinationPath;
        $message->setContent($content);
    }

    private function ensureString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (string) $value;
        }

        return '';
    }

    private function ensureInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }

        return 0;
    }

    /**
     * @param array<string, mixed> $datum
     */
    private function setSwitchMessage(ArchiveMessage $message, array $datum): void
    {
        $user = $datum['user'] ?? null;
        $message->setFromUserId(null !== $user ? $this->ensureString($user) : null);

        $time = $this->ensureInt($datum['time'] ?? 0);
        $message->setMsgTime(CarbonImmutable::createFromTimestampMs($time)->toDateTimeImmutable());
    }

    /**
     * @param array<string, mixed> $datum
     */
    private function setChatMessage(ArchiveMessage $message, array $datum): void
    {
        $from = $datum['from'] ?? null;
        $message->setFromUserId(null !== $from ? $this->ensureString($from) : null);

        $toList = $datum['tolist'] ?? null;
        if (is_array($toList)) {
            $stringList = [];
            foreach ($toList as $item) {
                $stringItem = $this->ensureString($item);
                if ('' !== $stringItem) {
                    $stringList[] = $stringItem;
                }
            }
            $message->setToList($stringList);
        } else {
            $message->setToList(null);
        }

        $roomId = $datum['roomid'] ?? null;
        $message->setRoomId(null !== $roomId ? $this->ensureString($roomId) : null);

        $msgTime = $this->ensureInt($datum['msgtime'] ?? 0);
        $message->setMsgTime(CarbonImmutable::createFromTimestampMs($msgTime)->toDateTimeImmutable());
    }

    /**
     * @param array<string, mixed> $datum
     */
    private function setMessageContent(ArchiveMessage $message, array $datum): void
    {
        if (!isset($datum['msgtype'])) {
            return;
        }

        $msgType = $this->ensureString($datum['msgtype']);
        $message->setMsgType($msgType);

        $content = $datum[$msgType] ?? null;
        if (is_array($content)) {
            /** @var array<string, mixed> $typedContent */
            $typedContent = $content;
            $message->setContent($typedContent);
        }
    }
}
