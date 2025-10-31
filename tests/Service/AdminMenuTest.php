<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatWorkMsgAuditBundle\Service\AdminMenu;

/**
 * AdminMenu服务测试
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testInvokeAddsMenuItems(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证菜单结构
        $wechatWorkMenu = $rootItem->getChild('企业微信');
        self::assertNotNull($wechatWorkMenu);

        $msgAuditMenu = $wechatWorkMenu->getChild('会话存档管理');
        self::assertNotNull($msgAuditMenu);

        $archiveMessageMenu = $msgAuditMenu->getChild('归档消息管理');
        self::assertNotNull($archiveMessageMenu);
    }

    public function testMenuStructure(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证图标设置
        $wechatWorkMenu = $rootItem->getChild('企业微信');
        self::assertNotNull($wechatWorkMenu);

        $msgAuditMenu = $wechatWorkMenu->getChild('会话存档管理');
        self::assertNotNull($msgAuditMenu);

        $archiveMessageMenu = $msgAuditMenu->getChild('归档消息管理');
        self::assertNotNull($archiveMessageMenu);

        self::assertSame('fas fa-archive', $msgAuditMenu->getAttribute('icon'));
        self::assertSame('fas fa-comments', $archiveMessageMenu->getAttribute('icon'));
    }

    public function testMenuItemExists(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 先添加一个已存在的"企业微信"菜单
        $rootItem->addChild('企业微信');

        $adminMenu->__invoke($rootItem);

        // 验证不会重复创建菜单
        $wechatWorkMenu = $rootItem->getChild('企业微信');
        self::assertNotNull($wechatWorkMenu);

        // 计算企业微信子菜单数量，应该至少有会话存档管理
        $children = $wechatWorkMenu->getChildren();
        self::assertGreaterThanOrEqual(1, count($children));
    }

    public function testServiceCanBeInstantiated(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);

        self::assertInstanceOf(AdminMenu::class, $adminMenu);
    }
}
