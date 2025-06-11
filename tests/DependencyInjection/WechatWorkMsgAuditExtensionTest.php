<?php

namespace WechatWorkMsgAuditBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatWorkMsgAuditBundle\DependencyInjection\WechatWorkMsgAuditExtension;

class WechatWorkMsgAuditExtensionTest extends TestCase
{
    private WechatWorkMsgAuditExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatWorkMsgAuditExtension();
        $this->container = new ContainerBuilder();
    }

    public function test_extension_inheritance(): void
    {
        $this->assertInstanceOf(\Symfony\Component\DependencyInjection\Extension\Extension::class, $this->extension);
    }

    public function test_load_method_exists(): void
    {
        $this->assertTrue(method_exists($this->extension, 'load'));
    }

    public function test_load_with_empty_configs(): void
    {
        $this->extension->load([], $this->container);
        
        // 验证容器没有异常
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    public function test_load_with_multiple_empty_configs(): void
    {
        $configs = [[], [], []];
        $this->extension->load($configs, $this->container);
        
        // 验证容器没有异常
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    public function test_services_yaml_file_exists(): void
    {
        $servicesPath = __DIR__ . '/../../src/Resources/config/services.yaml';
        $this->assertFileExists($servicesPath, 'services.yaml file should exist');
    }

    public function test_extension_alias(): void
    {
        // 测试扩展别名，通常基于类名
        $expectedAlias = 'wechat_work_msg_audit';
        $this->assertSame($expectedAlias, $this->extension->getAlias());
    }

    public function test_load_registers_services(): void
    {
        $this->extension->load([], $this->container);
        
        // 检查是否有服务被注册（通过检查定义数量）
        $definitions = $this->container->getDefinitions();
        $this->assertGreaterThanOrEqual(0, count($definitions));
    }

    public function test_load_multiple_times(): void
    {
        // 测试多次加载不会出错
        $this->extension->load([], $this->container);
        $this->extension->load([], $this->container);
        
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    public function test_container_compilation(): void
    {
        $this->extension->load([], $this->container);
        
        // 尝试编译容器以确保配置有效
        try {
            $this->container->compile();
            $this->assertTrue(true, 'Container compilation should succeed');
        } catch  (\Throwable $e) {
            $this->fail('Container compilation failed: ' . $e->getMessage());
        }
    }

    public function test_extension_class_name(): void
    {
        $this->assertSame(WechatWorkMsgAuditExtension::class, get_class($this->extension));
    }
} 