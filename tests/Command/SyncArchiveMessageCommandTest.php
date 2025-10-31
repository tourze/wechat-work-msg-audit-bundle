<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkMsgAuditBundle\Command\SyncArchiveMessageCommand;

/**
 * @internal
 */
#[CoversClass(SyncArchiveMessageCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncArchiveMessageCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 测试环境下无法提供外部依赖服务
    }

    protected function getCommandTester(): CommandTester
    {
        /** @var SyncArchiveMessageCommand $command */
        $command = self::getContainer()->get(SyncArchiveMessageCommand::class);

        return new CommandTester($command);
    }

    public function testCommandBasicProperties(): void
    {
        $this->assertEquals('wechat-work:sync-archive-message', SyncArchiveMessageCommand::NAME);
    }

    public function testCommandClass(): void
    {
        $reflection = new \ReflectionClass(SyncArchiveMessageCommand::class);
        $this->assertTrue($reflection->isSubclassOf(Command::class));
    }

    public function testCommandHasRequiredAttributes(): void
    {
        $reflection = new \ReflectionClass(SyncArchiveMessageCommand::class);

        $autoconfigureAttributes = $reflection->getAttributes(Autoconfigure::class);
        $this->assertCount(1, $autoconfigureAttributes);

        $asCommandAttributes = $reflection->getAttributes(AsCommand::class);
        $this->assertCount(1, $asCommandAttributes);

        $asCronTaskAttributes = $reflection->getAttributes(AsCronTask::class);
        $this->assertCount(1, $asCronTaskAttributes);
    }

    public function testCommandStructure(): void
    {
        $reflection = new \ReflectionClass(SyncArchiveMessageCommand::class);

        $this->assertTrue($reflection->hasMethod('execute'));
        $this->assertTrue($reflection->hasMethod('configure'));
        $this->assertTrue($reflection->hasConstant('NAME'));

        $this->assertTrue($reflection->isInstantiable());
        $this->assertFalse($reflection->isAbstract());
    }

    public function testCommandTesterIntegration(): void
    {
        // 由于命令依赖外部服务（FilesystemOperator），运行时会失败
        // 但我们可以测试命令能够被正确初始化
        $commandTester = $this->getCommandTester();
        $this->assertInstanceOf(CommandTester::class, $commandTester);

        // 测试命令基本功能可用
        /** @var SyncArchiveMessageCommand $command */
        $command = self::getContainer()->get(SyncArchiveMessageCommand::class);
        $this->assertEquals('wechat-work:sync-archive-message', $command->getName());
    }

    public function testArgumentCorpId(): void
    {
        // 从服务容器中获取命令实例
        /** @var SyncArchiveMessageCommand $command */
        $command = self::getContainer()->get(SyncArchiveMessageCommand::class);

        // 验证 corpId 参数存在且配置正确
        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasArgument('corpId'));

        $corpIdArgument = $definition->getArgument('corpId');
        $this->assertFalse($corpIdArgument->isRequired());
        $this->assertEquals('企业ID', $corpIdArgument->getDescription());
    }

    public function testArgumentAgentId(): void
    {
        // 从服务容器中获取命令实例
        /** @var SyncArchiveMessageCommand $command */
        $command = self::getContainer()->get(SyncArchiveMessageCommand::class);

        // 验证 agentId 参数存在且配置正确
        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasArgument('agentId'));

        $agentIdArgument = $definition->getArgument('agentId');
        $this->assertFalse($agentIdArgument->isRequired());
        $this->assertEquals('应用ID', $agentIdArgument->getDescription());
    }
}
