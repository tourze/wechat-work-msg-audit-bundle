<?php

namespace WechatWorkMsgAuditBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatWorkMsgAuditBundle\DependencyInjection\WechatWorkMsgAuditExtension;

/**
 * @internal
 */
#[CoversClass(WechatWorkMsgAuditExtension::class)]
final class WechatWorkMsgAuditExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testGetAlias(): void
    {
        $extension = new WechatWorkMsgAuditExtension();
        $this->assertEquals('wechat_work_msg_audit', $extension->getAlias());
    }

    public function testExtensionClass(): void
    {
        $reflection = new \ReflectionClass(WechatWorkMsgAuditExtension::class);
        $this->assertTrue($reflection->hasMethod('load'));
        $this->assertTrue($reflection->hasMethod('getAlias'));
        $this->assertTrue($reflection->hasMethod('getNamespace'));
    }

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [
            new WechatWorkMsgAuditExtension(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getMinimalConfiguration(): array
    {
        return [];
    }
}
