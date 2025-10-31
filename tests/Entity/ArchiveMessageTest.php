<?php

namespace WechatWorkMsgAuditBundle\Tests\Entity;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

/**
 * @internal
 */
#[CoversClass(ArchiveMessage::class)]
final class ArchiveMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): ArchiveMessage
    {
        return new ArchiveMessage();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     */
    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $time = new \DateTimeImmutable('2023-11-15 10:30:00');

        yield 'context' => ['context', ['msgid' => 'test', 'action' => 'send']];
        yield 'msgId' => ['msgId', 'test_msg_123'];
        yield 'action' => ['action', 'send'];
        yield 'fromUserId' => ['fromUserId', 'user123'];
        yield 'toList' => ['toList', ['user1', 'user2']];
        yield 'msgTime' => ['msgTime', $time];
        yield 'seq' => ['seq', 16];
        yield 'roomId' => ['roomId', 'room123'];
        yield 'msgType' => ['msgType', 'text'];
        yield 'content' => ['content', ['text' => 'Hello World']];
        yield 'corp' => ['corp', null];
    }

    public function testIdGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getId());

        // ID通常由Snowflake生成器自动设置，这里测试getter
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, '123456789');

        $this->assertSame('123456789', $entity->getId());
    }

    public function testContextGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertSame([], $entity->getContext());

        $context = [
            'msgid' => '16666922367251035000_1668250656103',
            'action' => 'send',
            'time' => 1668250655937,
        ];

        $entity->setContext($context);
        $this->assertSame($context, $entity->getContext());

        // 测试null值
        $entity->setContext(null);
        $this->assertNull($entity->getContext());
    }

    public function testMsgIdGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getMsgId());

        $msgId = '16666922367251035000_1668250656103';
        $entity->setMsgId($msgId);
        $this->assertSame($msgId, $entity->getMsgId());
    }

    public function testActionGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getAction());

        $entity->setAction('send');
        $this->assertSame('send', $entity->getAction());

        $entity->setAction('recall');
        $this->assertSame('recall', $entity->getAction());

        $entity->setAction('switch');
        $this->assertSame('switch', $entity->getAction());
    }

    public function testFromUserIdGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getFromUserId());

        $userId = 'felix_ye';
        $entity->setFromUserId($userId);
        $this->assertSame($userId, $entity->getFromUserId());

        // 测试null值
        $entity->setFromUserId(null);
        $this->assertNull($entity->getFromUserId());
    }

    public function testToListGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertSame([], $entity->getToList());

        $toList = ['user1', 'user2', 'external_user1'];
        $entity->setToList($toList);
        $this->assertSame($toList, $entity->getToList());

        // 测试空数组
        $entity->setToList([]);
        $this->assertSame([], $entity->getToList());
    }

    public function testMsgTimeGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getMsgTime());

        $time = new \DateTimeImmutable('2023-11-15 10:30:00');
        $entity->setMsgTime($time);
        $this->assertSame($time, $entity->getMsgTime());

        // 测试Carbon实例
        $carbonTime = CarbonImmutable::now()->toDateTimeImmutable();
        $entity->setMsgTime($carbonTime);
        $this->assertSame($carbonTime, $entity->getMsgTime());
    }

    public function testSeqGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getSeq());

        $entity->setSeq(16);
        $this->assertSame(16, $entity->getSeq());

        $entity->setSeq(0);
        $this->assertSame(0, $entity->getSeq());

        $entity->setSeq(-1);
        $this->assertSame(-1, $entity->getSeq());
    }

    public function testRoomIdGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getRoomId());

        $roomId = 'room_123456';
        $entity->setRoomId($roomId);
        $this->assertSame($roomId, $entity->getRoomId());

        // 测试null值（单聊）
        $entity->setRoomId(null);
        $this->assertNull($entity->getRoomId());
    }

    public function testMsgTypeGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getMsgType());

        $entity->setMsgType('text');
        $this->assertSame('text', $entity->getMsgType());

        $entity->setMsgType('image');
        $this->assertSame('image', $entity->getMsgType());

        $entity->setMsgType('voice');
        $this->assertSame('voice', $entity->getMsgType());

        $entity->setMsgType('video');
        $this->assertSame('video', $entity->getMsgType());

        // 测试null值
        $entity->setMsgType(null);
        $this->assertNull($entity->getMsgType());
    }

    public function testContentGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertSame([], $entity->getContent());

        $content = [
            'content' => 'Hello World',
            'type' => 'text',
        ];

        $entity->setContent($content);
        $this->assertSame($content, $entity->getContent());

        // 测试复杂内容（媒体文件）
        $mediaContent = [
            'sdkfileid' => 'abc123def456',
            'filename' => 'image.png',
            'fileKey' => 'archive/2023/11/15/abc123def456.png',
        ];

        $entity->setContent($mediaContent);
        $this->assertSame($mediaContent, $entity->getContent());

        // 测试空数组
        $entity->setContent([]);
        $this->assertSame([], $entity->getContent());
    }

    public function testCorpGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getCorp());

        // 由于CorpInterface是一个接口，我们只能测试null值的设置
        // 实际的corp对象需要在数据库中创建并关联
        $entity->setCorp(null);
        $this->assertNull($entity->getCorp());
    }

    public function testEntitySetters(): void
    {
        $time = new \DateTimeImmutable();
        $entity = $this->createEntity();

        // 测试各个setter方法（不进行链式调用，因为返回void）
        $entity->setMsgId('test_msg_id');
        $entity->setAction('send');
        $entity->setFromUserId('user123');
        $entity->setToList(['user1', 'user2']);
        $entity->setMsgTime($time);
        $entity->setSeq(10);
        $entity->setRoomId('room123');
        $entity->setMsgType('text');
        $entity->setContent(['content' => 'Hello']);
        $entity->setCorp(null);
        $entity->setContext(['test' => 'data']);

        // 验证所有值都被正确设置
        $this->assertSame('test_msg_id', $entity->getMsgId());
        $this->assertSame('send', $entity->getAction());
        $this->assertSame('user123', $entity->getFromUserId());
        $this->assertSame(['user1', 'user2'], $entity->getToList());
        $this->assertSame($time, $entity->getMsgTime());
        $this->assertSame(10, $entity->getSeq());
        $this->assertSame('room123', $entity->getRoomId());
        $this->assertSame('text', $entity->getMsgType());
        $this->assertSame(['content' => 'Hello'], $entity->getContent());
        $this->assertNull($entity->getCorp());
        $this->assertSame(['test' => 'data'], $entity->getContext());
    }

    public function testCompleteMessageScenario(): void
    {
        $time = CarbonImmutable::createFromTimestampMs(1668250655937)->toDateTimeImmutable();
        $entity = $this->createEntity();

        // 模拟完整的消息数据
        $entity->setMsgId('16666922367251035000_1668250656103');
        $entity->setAction('send');
        $entity->setFromUserId('felix_ye');
        $entity->setToList(['user1@corp', 'user2@corp']);
        $entity->setMsgTime($time);
        $entity->setSeq(16);
        $entity->setRoomId('room_group_chat');
        $entity->setMsgType('image');
        $entity->setContent([
            'sdkfileid' => 'abc123def456ghi789',
            'filename' => 'screenshot.png',
            'fileKey' => 'archive/2023/11/15/abc123def456ghi789.png',
            'size' => 1024000,
        ]);
        $entity->setCorp(null);
        $entity->setContext([
            'msgid' => '16666922367251035000_1668250656103',
            'action' => 'send',
            'from' => 'felix_ye',
            'tolist' => ['user1@corp', 'user2@corp'],
            'msgtime' => 1668250655937,
            'msgtype' => 'image',
            'roomid' => 'room_group_chat',
            'seq' => 16,
            'image' => [
                'sdkfileid' => 'abc123def456ghi789',
                'filename' => 'screenshot.png',
            ],
        ]);

        // 验证所有数据都正确设置
        $this->assertSame('16666922367251035000_1668250656103', $entity->getMsgId());
        $this->assertSame('send', $entity->getAction());
        $this->assertSame('felix_ye', $entity->getFromUserId());
        $this->assertSame(['user1@corp', 'user2@corp'], $entity->getToList());
        $this->assertEquals($time, $entity->getMsgTime());
        $this->assertSame(16, $entity->getSeq());
        $this->assertSame('room_group_chat', $entity->getRoomId());
        $this->assertSame('image', $entity->getMsgType());
        $content = $entity->getContent();
        $this->assertIsArray($content);
        $this->assertArrayHasKey('fileKey', $content);
        $this->assertNull($entity->getCorp());
        $context = $entity->getContext();
        $this->assertIsArray($context);
        $this->assertArrayHasKey('msgid', $context);
    }
}
