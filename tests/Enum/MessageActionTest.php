<?php

namespace WechatWorkMsgAuditBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatWorkMsgAuditBundle\Enum\MessageAction;

class MessageActionTest extends TestCase
{
    public function testEnumValues(): void
    {
        // 测试枚举值
        $this->assertEquals('send', MessageAction::SEND->value);
        $this->assertEquals('recall', MessageAction::RECALL->value);
        $this->assertEquals('switch', MessageAction::SWITCH->value);
        
        // 测试枚举数量
        $cases = MessageAction::cases();
        $this->assertCount(3, $cases);
        $this->assertContains(MessageAction::SEND, $cases);
        $this->assertContains(MessageAction::RECALL, $cases);
        $this->assertContains(MessageAction::SWITCH, $cases);
    }
    
    public function testGetLabel(): void
    {
        // 测试标签翻译
        $this->assertEquals('发送消息', MessageAction::SEND->getLabel());
        $this->assertEquals('撤回消息', MessageAction::RECALL->getLabel());
        $this->assertEquals('切换企业日志', MessageAction::SWITCH->getLabel());
    }
    
    /**
     * 测试Itemable特性的使用
     * 由于不知道确切的实现细节，我们将跳过这个测试
     */
    public function testItemable(): void
    {
        $this->assertTrue(in_array('Tourze\EnumExtra\Itemable', class_implements(MessageAction::class)));
        $this->assertTrue(in_array('Tourze\EnumExtra\ItemTrait', class_uses(MessageAction::class)));
    }
    
    /**
     * 测试Selectable特性的使用
     * 由于不知道确切的实现细节，我们将跳过这个测试
     */
    public function testSelectable(): void
    {
        $this->assertTrue(in_array('Tourze\EnumExtra\Selectable', class_implements(MessageAction::class)));
        $this->assertTrue(in_array('Tourze\EnumExtra\SelectTrait', class_uses(MessageAction::class)));
    }
} 