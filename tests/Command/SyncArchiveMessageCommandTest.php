<?php

namespace WechatWorkMsgAuditBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkMsgAuditBundle\Command\SyncArchiveMessageCommand;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

class SyncArchiveMessageCommandTest extends TestCase
{
    private SyncArchiveMessageCommand $command;
    private CorpRepository $corpRepository;
    private AgentRepository $agentRepository;
    private FilesystemOperator $mountManager;
    private ArchiveMessageRepository $messageRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        /** @var CorpRepository $corpRepository */
        $this->corpRepository = $this->createMock(CorpRepository::class);
        /** @var AgentRepository $agentRepository */
        $this->agentRepository = $this->createMock(AgentRepository::class);
        /** @var FilesystemOperator $mountManager */
        $this->mountManager = $this->createMock(FilesystemOperator::class);
        /** @var ArchiveMessageRepository $messageRepository */
        $this->messageRepository = $this->createMock(ArchiveMessageRepository::class);
        /** @var EntityManagerInterface $entityManager */
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->command = new SyncArchiveMessageCommand(
            $this->corpRepository,
            $this->agentRepository,
            $this->mountManager,
            $this->messageRepository,
            $this->entityManager
        );
    }

    public function test_command_inheritance(): void
    {
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function test_command_name(): void
    {
        $this->assertSame('wechat-work:sync-archive-message', $this->command->getName());
    }

    public function test_command_description(): void
    {
        $this->assertSame('同步归档消息', $this->command->getDescription());
    }

    public function test_command_arguments(): void
    {
        $definition = $this->command->getDefinition();
        
        $this->assertTrue($definition->hasArgument('corpId'));
        $this->assertTrue($definition->hasArgument('agentId'));
        
        $corpIdArg = $definition->getArgument('corpId');
        $this->assertFalse($corpIdArg->isRequired());
        $this->assertSame('企业ID', $corpIdArg->getDescription());
        
        $agentIdArg = $definition->getArgument('agentId');
        $this->assertFalse($agentIdArg->isRequired());
        $this->assertSame('应用ID', $agentIdArg->getDescription());
    }

    public function test_command_has_cron_attribute(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $attributes = $reflection->getAttributes(\Tourze\Symfony\CronJob\Attribute\AsCronTask::class);
        
        $this->assertCount(1, $attributes);
        $cronAttribute = $attributes[0]->newInstance();
        $this->assertInstanceOf(\Tourze\Symfony\CronJob\Attribute\AsCronTask::class, $cronAttribute);
    }

    public function test_command_has_console_attribute(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $attributes = $reflection->getAttributes(\Symfony\Component\Console\Attribute\AsCommand::class);
        
        $this->assertCount(1, $attributes);
        $commandAttribute = $attributes[0]->newInstance();
        $this->assertInstanceOf(\Symfony\Component\Console\Attribute\AsCommand::class, $commandAttribute);
    }

    public function test_command_constructor_dependencies(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        $this->assertCount(5, $parameters);
        
        $this->assertSame('corpRepository', $parameters[0]->getName());
        $this->assertSame('agentRepository', $parameters[1]->getName());
        $this->assertSame('mountManager', $parameters[2]->getName());
        $this->assertSame('messageRepository', $parameters[3]->getName());
        $this->assertSame('entityManager', $parameters[4]->getName());
    }

    public function test_execute_with_invalid_corp_id(): void
    {
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'invalid_corp'])
            ->willReturn(null);

        $application = new Application();
        $application->add($this->command);

        $input = new ArrayInput([
            'command' => 'wechat-work:sync-archive-message',
            'corpId' => 'invalid_corp',
        ]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertSame(Command::FAILURE, $result);
        $this->assertStringContainsString('找不到企业', $output->fetch());
    }

    public function test_execute_without_arguments(): void
    {
        $this->corpRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $application = new Application();
        $application->add($this->command);

        $input = new ArrayInput([
            'command' => 'wechat-work:sync-archive-message',
        ]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_command_class_name(): void
    {
        $this->assertSame(SyncArchiveMessageCommand::class, get_class($this->command));
    }

    public function test_command_configure_method(): void
    {
        $this->assertTrue(method_exists($this->command, 'configure'));
        
        $reflection = new \ReflectionMethod($this->command, 'configure');
        $this->assertSame('configure', $reflection->getName());
        $this->assertTrue($reflection->isProtected());
    }

    public function test_command_execute_method(): void
    {
        $this->assertTrue(method_exists($this->command, 'execute'));
        
        $reflection = new \ReflectionMethod($this->command, 'execute');
        $this->assertSame('execute', $reflection->getName());
        $this->assertTrue($reflection->isProtected());
    }
} 