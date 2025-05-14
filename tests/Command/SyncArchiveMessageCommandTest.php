<?php

namespace WechatWorkMsgAuditBundle\Tests\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Enum\SpecialAgent;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkMsgAuditBundle\Command\SyncArchiveMessageCommand;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

/**
 * 注意：这个测试依赖于MoChat\WeWorkFinanceSDK\WxFinanceSDK静态方法，测试时需要修改方法以使用mock
 * 为了避免修改源代码，我们在测试中避免实际调用SDK，并在必要时跳过相关测试
 */
class SyncArchiveMessageCommandTest extends TestCase
{
    private $corpRepository;
    private $agentRepository;
    private $mountManager;
    private $messageRepository;
    private $entityManager;
    private $command;
    private $commandTester;
    
    protected function setUp(): void
    {
        // 创建模拟依赖
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->mountManager = $this->createMock(FilesystemOperator::class);
        $this->messageRepository = $this->createMock(ArchiveMessageRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        // 创建命令
        $this->command = new SyncArchiveMessageCommand(
            $this->corpRepository,
            $this->agentRepository,
            $this->mountManager,
            $this->messageRepository,
            $this->entityManager
        );
        
        // 创建应用和命令测试器
        $application = new Application();
        $application->add($this->command);
        $this->commandTester = new CommandTester($this->command);
    }
    
    public function testExecuteWithInvalidCorpId(): void
    {
        // 配置mock返回无效企业
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'invalid_corp_id'])
            ->willReturn(null);
        
        // 执行命令
        $this->commandTester->execute([
            'corpId' => 'invalid_corp_id',
        ]);
        
        // 检查输出和退出码
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('找不到企业', $output);
        $this->assertEquals(1, $this->commandTester->getStatusCode());
    }
    
    public function testExecuteWithValidCorpButNoAgent(): void
    {
        // 创建模拟Corp
        $corp = $this->createMock(Corp::class);
        $corp->method('getName')->willReturn('测试企业');
        
        // 为Corp创建空的Agent集合
        $emptyCollection = new ArrayCollection();
        $corp->method('getAgents')->willReturn($emptyCollection);
        
        // 配置mock返回有效企业
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'valid_corp_id'])
            ->willReturn($corp);
        
        // 配置agent mock
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => '123456',
            ])
            ->willReturn(null);
        
        // 执行命令
        $this->commandTester->execute([
            'corpId' => 'valid_corp_id',
            'agentId' => '123456',
        ]);
        
        // 检查输出
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('找不到[测试企业]的消息归档应用', $output);
    }
    
    public function testExecuteWithValidCorpAndAgentButNoPrivateKey(): void
    {
        // 创建模拟Corp
        $corp = $this->createMock(Corp::class);
        $corp->method('getName')->willReturn('测试企业');
        $corp->method('getCorpId')->willReturn('wxCorpId');
        
        // 创建模拟Agent
        $agent = $this->createMock(Agent::class);
        $agent->method('getAgentId')->willReturn(SpecialAgent::MESSAGE_ARCHIVE->value);
        $agent->method('getPrivateKeyContent')->willReturn(''); // 空私钥
        
        // 为Corp创建包含Agent的集合
        $agentCollection = new ArrayCollection([$agent]);
        $corp->method('getAgents')->willReturn($agentCollection);
        
        // 配置mock返回企业列表
        $this->corpRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$corp]);
        
        // 执行命令（不传参数，测试全部企业）
        $this->commandTester->execute([]);
        
        // 检查输出
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('正在归档[测试企业]的消息', $output);
        $this->assertStringContainsString('[测试企业]未配置秘钥信息，不能同步', $output);
    }
    
    public function testExecuteWithExistingMessage(): void
    {
        $this->markTestSkipped('由于依赖外部SDK静态方法，此测试被跳过');
        
        // TODO: 如需真实测试，需要使用扩展功能来模拟WxFinanceSDK静态方法
    }
} 