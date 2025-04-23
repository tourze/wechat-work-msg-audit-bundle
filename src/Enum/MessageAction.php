<?php

namespace WechatWorkMsgAuditBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MessageAction: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case SEND = 'send';
    case RECALL = 'recall';
    case SWITCH = 'switch';

    public function getLabel(): string
    {
        return match ($this) {
            self::SEND => '发送消息',
            self::RECALL => '撤回消息',
            self::SWITCH => '切换企业日志',
        };
    }
}
