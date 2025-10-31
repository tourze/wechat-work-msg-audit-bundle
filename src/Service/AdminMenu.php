<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

/**
 * 企业微信会话内容存档管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('企业微信')) {
            $item->addChild('企业微信');
        }

        $wechatWorkMenu = $item->getChild('企业微信');
        if (null === $wechatWorkMenu) {
            return;
        }

        // 添加会话存档管理子菜单
        if (null === $wechatWorkMenu->getChild('会话存档管理')) {
            $wechatWorkMenu->addChild('会话存档管理')
                ->setAttribute('icon', 'fas fa-archive')
            ;
        }

        $msgAuditMenu = $wechatWorkMenu->getChild('会话存档管理');
        if (null === $msgAuditMenu) {
            return;
        }

        $msgAuditMenu->addChild('归档消息管理')
            ->setUri($this->linkGenerator->getCurdListPage(ArchiveMessage::class))
            ->setAttribute('icon', 'fas fa-comments')
        ;
    }
}
