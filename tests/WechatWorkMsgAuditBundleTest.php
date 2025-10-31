<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatWorkMsgAuditBundle\WechatWorkMsgAuditBundle;

/**
 * @internal
 */
#[CoversClass(WechatWorkMsgAuditBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkMsgAuditBundleTest extends AbstractBundleTestCase
{
}
