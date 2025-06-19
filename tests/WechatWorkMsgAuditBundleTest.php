<?php

namespace WechatWorkMsgAuditBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WechatWorkMsgAuditBundle\WechatWorkMsgAuditBundle;

class WechatWorkMsgAuditBundleTest extends TestCase
{
    public function test_bundle_instance_creation(): void
    {
        $bundle = new WechatWorkMsgAuditBundle();
        
        $this->assertInstanceOf(WechatWorkMsgAuditBundle::class, $bundle);
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function test_bundle_name_is_correct(): void
    {
        $bundle = new WechatWorkMsgAuditBundle();
        
        $this->assertSame('WechatWorkMsgAuditBundle', $bundle->getName());
    }

    public function test_bundle_namespace_is_correct(): void
    {
        $bundle = new WechatWorkMsgAuditBundle();
        
        $this->assertSame('WechatWorkMsgAuditBundle', $bundle->getNamespace());
    }

    public function test_bundle_path_contains_expected_directory(): void
    {
        $bundle = new WechatWorkMsgAuditBundle();
        
        $path = $bundle->getPath();
        $this->assertStringContainsString('wechat-work-msg-audit-bundle', $path);
    }
} 