<?php

namespace WechatWorkMsgAuditBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatWorkMsgAuditBundle\Enum\MessageAction;

class MessageActionTest extends TestCase
{
    public function test_enum_values_are_correct(): void
    {
        $this->assertSame('send', MessageAction::SEND->value);
        $this->assertSame('recall', MessageAction::RECALL->value);
        $this->assertSame('switch', MessageAction::SWITCH->value);
    }

    public function test_getLabel_returns_correct_labels(): void
    {
        $this->assertSame('发送消息', MessageAction::SEND->getLabel());
        $this->assertSame('撤回消息', MessageAction::RECALL->getLabel());
        $this->assertSame('切换企业日志', MessageAction::SWITCH->getLabel());
    }

    public function test_enum_implements_required_interfaces(): void
    {
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, MessageAction::SEND);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, MessageAction::SEND);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, MessageAction::SEND);
    }

    public function test_toSelectItem_returns_proper_format(): void
    {
        $item = MessageAction::SEND->toSelectItem();
        $this->assertArrayHasKey('label', $item);
        $this->assertArrayHasKey('text', $item);
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertSame('发送消息', $item['label']);
        $this->assertSame('发送消息', $item['text']);
        $this->assertSame('send', $item['value']);
        $this->assertSame('发送消息', $item['name']);
    }

    public function test_toArray_returns_proper_format(): void
    {
        $array = MessageAction::SEND->toArray();
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame('send', $array['value']);
        $this->assertSame('发送消息', $array['label']);
    }

    public function test_genOptions_returns_all_enum_options(): void
    {
        $options = MessageAction::genOptions();
        $this->assertCount(3, $options);
        
        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('text', $option);
            $this->assertArrayHasKey('name', $option);
        }
    }

    public function test_all_enum_cases_exist(): void
    {
        $cases = MessageAction::cases();
        
        $this->assertCount(3, $cases);
        $this->assertContains(MessageAction::SEND, $cases);
        $this->assertContains(MessageAction::RECALL, $cases);
        $this->assertContains(MessageAction::SWITCH, $cases);
    }

    public function test_from_returns_correct_enum_case(): void
    {
        $this->assertSame(MessageAction::SEND, MessageAction::from('send'));
        $this->assertSame(MessageAction::RECALL, MessageAction::from('recall'));
        $this->assertSame(MessageAction::SWITCH, MessageAction::from('switch'));
    }

    public function test_from_throws_exception_for_invalid_value(): void
    {
        $this->expectException(\ValueError::class);
        MessageAction::from('invalid');
    }

    public function test_tryFrom_returns_null_for_invalid_value(): void
    {
        $this->assertNull(MessageAction::tryFrom('invalid'));
        $this->assertNull(MessageAction::tryFrom(''));
    }

    public function test_tryFrom_returns_correct_enum_for_valid_value(): void
    {
        $this->assertSame(MessageAction::SEND, MessageAction::tryFrom('send'));
        $this->assertSame(MessageAction::RECALL, MessageAction::tryFrom('recall'));
        $this->assertSame(MessageAction::SWITCH, MessageAction::tryFrom('switch'));
    }
} 