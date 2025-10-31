<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Factory;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use WechatWorkMsgAuditBundle\Adapter\TestFilesystemAdapter;

class FilesystemFactory
{
    public function createFilesystem(?string $environment = null): FilesystemOperator
    {
        // 在测试环境下使用 stub 文件系统适配器
        if ('test' === $environment) {
            return new Filesystem(new TestFilesystemAdapter());
        }

        // 在其他环境下使用本地文件系统
        // 实际项目中应该从配置中读取路径
        $adapter = new LocalFilesystemAdapter('/tmp/wechat-work-msg-audit');

        return new Filesystem($adapter);
    }
}
