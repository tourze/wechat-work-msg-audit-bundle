<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;
use WechatWorkMsgAuditBundle\Enum\MessageAction;

/**
 * 归档消息管理控制器
 */
#[AdminCrud(routePath: '/wechat-work-msg-audit/archive-message', routeName: 'wechat_work_msg_audit_archive_message')]
final class ArchiveMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArchiveMessage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $actionField = EnumField::new('action', '消息动作')
            ->setRequired(true)
        ;
        $actionField->setEnumCases(MessageAction::cases());
        $actionField->setHelp('消息的操作类型');

        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('msgId', '消息ID')
                ->setRequired(true)
                ->setHelp('消息的唯一标识')
                ->setMaxLength(120),

            $actionField,

            TextField::new('fromUserId', '发送方ID')
                ->setRequired(false)
                ->setHelp('消息发送方用户ID（同企业为userid，非同企业或机器人为external_userid）')
                ->setMaxLength(100),

            ArrayField::new('toList', '接收方列表')
                ->setRequired(false)
                ->setHelp('消息接收方用户ID列表'),

            DateTimeField::new('msgTime', '消息时间')
                ->setRequired(true)
                ->setHelp('消息发送的时间')
                ->setFormat('yyyy-MM-dd HH:mm:ss'),

            IntegerField::new('seq', '消息序列号')
                ->setRequired(true)
                ->setHelp('消息的序列号')
                ->setFormTypeOptions(['attr' => ['min' => 0]]),

            TextField::new('roomId', '群聊ID')
                ->setRequired(false)
                ->setHelp('群聊消息的群ID（单聊则为空）')
                ->setMaxLength(100),

            TextField::new('msgType', '消息类型')
                ->setRequired(false)
                ->setHelp('消息的类型')
                ->setMaxLength(40),

            ArrayField::new('content', '消息内容')
                ->setRequired(false)
                ->setHelp('消息的具体内容')
                ->hideOnIndex(),

            ArrayField::new('context', '上下文')
                ->setRequired(false)
                ->setHelp('消息相关的上下文信息')
                ->hideOnIndex(),

            AssociationField::new('corp', '所属企业')
                ->setRequired(true)
                ->setHelp('消息所属的企业微信公司'),

            DateTimeField::new('createdAt', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),

            DateTimeField::new('updatedAt', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('msgId')
            ->add('action')
            ->add('fromUserId')
            ->add('msgTime')
            ->add('msgType')
            ->add('corp')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('归档消息')
            ->setEntityLabelInPlural('归档消息')
            ->setPageTitle('index', '归档消息管理')
            ->setPageTitle('new', '创建归档消息')
            ->setPageTitle('edit', '编辑归档消息')
            ->setPageTitle('detail', '查看归档消息')
            ->setDefaultSort(['msgTime' => 'DESC'])
            ->setPaginatorPageSize(50)
        ;
    }
}
