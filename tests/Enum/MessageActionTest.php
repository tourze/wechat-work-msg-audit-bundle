<?php

namespace WechatWorkMsgAuditBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatWorkMsgAuditBundle\Enum\MessageAction;

/**
 * @internal
 */
#[CoversClass(MessageAction::class)]
final class MessageActionTest extends AbstractEnumTestCase
{
    public function testEnumValuesAreCorrect(): void
    {
        $this->assertSame('send', MessageAction::SEND->value);
        $this->assertSame('recall', MessageAction::RECALL->value);
        $this->assertSame('switch', MessageAction::SWITCH->value);
    }

    public function testGetLabelReturnsCorrectLabels(): void
    {
        $this->assertSame('发送消息', MessageAction::SEND->getLabel());
        $this->assertSame('撤回消息', MessageAction::RECALL->getLabel());
        $this->assertSame('切换企业日志', MessageAction::SWITCH->getLabel());
    }

    public function testEnumImplementsRequiredInterfaces(): void
    {
        $this->assertInstanceOf(Labelable::class, MessageAction::SEND);
        $this->assertInstanceOf(Itemable::class, MessageAction::SEND);
        $this->assertInstanceOf(Selectable::class, MessageAction::SEND);
    }

    public function testToSelectItemReturnsProperFormat(): void
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

    public function testToArrayReturnsProperFormat(): void
    {
        $array = MessageAction::SEND->toArray();
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame('send', $array['value']);
        $this->assertSame('发送消息', $array['label']);
    }

    public function testGenOptionsReturnsAllEnumOptions(): void
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

    public function testAllEnumCasesExist(): void
    {
        $cases = MessageAction::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(MessageAction::SEND, $cases);
        $this->assertContains(MessageAction::RECALL, $cases);
        $this->assertContains(MessageAction::SWITCH, $cases);
    }

    public function testFromReturnsCorrectEnumCase(): void
    {
        $this->assertSame(MessageAction::SEND, MessageAction::from('send'));
        $this->assertSame(MessageAction::RECALL, MessageAction::from('recall'));
        $this->assertSame(MessageAction::SWITCH, MessageAction::from('switch'));
    }

    public function testFromThrowsExceptionForInvalidValue(): void
    {
        $this->expectException(\ValueError::class);
        MessageAction::from('invalid');
    }

    public function testTryFromReturnsNullForInvalidValue(): void
    {
        $this->assertNull(MessageAction::tryFrom('invalid'));
        $this->assertNull(MessageAction::tryFrom(''));
    }

    public function testTryFromReturnsCorrectEnumForValidValue(): void
    {
        $this->assertSame(MessageAction::SEND, MessageAction::tryFrom('send'));
        $this->assertSame(MessageAction::RECALL, MessageAction::tryFrom('recall'));
        $this->assertSame(MessageAction::SWITCH, MessageAction::tryFrom('switch'));
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (MessageAction $case) => $case->value, MessageAction::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (MessageAction $case) => $case->getLabel(), MessageAction::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }
}
