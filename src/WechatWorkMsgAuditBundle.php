<?php

namespace WechatWorkMsgAuditBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '消息存档')]
class WechatWorkMsgAuditBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \WechatWorkBundle\WechatWorkBundle::class => ['all' => true],
        ];
    }
}
