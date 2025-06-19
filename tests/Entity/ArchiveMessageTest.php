<?php

namespace WechatWorkMsgAuditBundle\Tests\Entity;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

class ArchiveMessageTest extends TestCase
{
    private ArchiveMessage $entity;

    protected function setUp(): void
    {
        $this->entity = new ArchiveMessage();
    }

    public function test_id_getter_and_setter(): void
    {
        $this->assertNull($this->entity->getId());
        
        // ID通常由Snowflake生成器自动设置，这里测试getter
        $reflection = new \ReflectionClass($this->entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->entity, '123456789');
        
        $this->assertSame('123456789', $this->entity->getId());
    }

    public function test_context_getter_and_setter(): void
    {
        $this->assertSame([], $this->entity->getContext());
        
        $context = [
            'msgid' => '16666922367251035000_1668250656103',
            'action' => 'send',
            'time' => 1668250655937,
        ];
        
        $this->entity->setContext($context);
        $this->assertSame($context, $this->entity->getContext());
        
        // 测试null值
        $this->entity->setContext(null);
        $this->assertNull($this->entity->getContext());
    }

    public function test_msgId_getter_and_setter(): void
    {
        $this->assertNull($this->entity->getMsgId());
        
        $msgId = '16666922367251035000_1668250656103';
        $this->entity->setMsgId($msgId);
        $this->assertSame($msgId, $this->entity->getMsgId());
    }

    public function test_action_getter_and_setter(): void
    {
        $this->assertNull($this->entity->getAction());
        
        $this->entity->setAction('send');
        $this->assertSame('send', $this->entity->getAction());
        
        $this->entity->setAction('recall');
        $this->assertSame('recall', $this->entity->getAction());
        
        $this->entity->setAction('switch');
        $this->assertSame('switch', $this->entity->getAction());
    }

    public function test_fromUserId_getter_and_setter(): void
    {
        $this->assertNull($this->entity->getFromUserId());
        
        $userId = 'felix_ye';
        $this->entity->setFromUserId($userId);
        $this->assertSame($userId, $this->entity->getFromUserId());
        
        // 测试null值
        $this->entity->setFromUserId(null);
        $this->assertNull($this->entity->getFromUserId());
    }

    public function test_toList_getter_and_setter(): void
    {
        $this->assertSame([], $this->entity->getToList());
        
        $toList = ['user1', 'user2', 'external_user1'];
        $this->entity->setToList($toList);
        $this->assertSame($toList, $this->entity->getToList());
        
        // 测试空数组
        $this->entity->setToList([]);
        $this->assertSame([], $this->entity->getToList());
    }

    public function test_msgTime_getter_and_setter(): void
    {
        $this->assertNull($this->entity->getMsgTime());
        
        $time = new \DateTimeImmutable('2023-11-15 10:30:00');
        $this->entity->setMsgTime($time);
        $this->assertSame($time, $this->entity->getMsgTime());
        
        // 测试Carbon实例
        $carbonTime = CarbonImmutable::now()->toDateTimeImmutable();
        $this->entity->setMsgTime($carbonTime);
        $this->assertSame($carbonTime, $this->entity->getMsgTime());
    }

    public function test_seq_getter_and_setter(): void
    {
        $this->assertNull($this->entity->getSeq());
        
        $this->entity->setSeq(16);
        $this->assertSame(16, $this->entity->getSeq());
        
        $this->entity->setSeq(0);
        $this->assertSame(0, $this->entity->getSeq());
        
        $this->entity->setSeq(-1);
        $this->assertSame(-1, $this->entity->getSeq());
    }

    public function test_roomId_getter_and_setter(): void
    {
        $this->assertNull($this->entity->getRoomId());
        
        $roomId = 'room_123456';
        $this->entity->setRoomId($roomId);
        $this->assertSame($roomId, $this->entity->getRoomId());
        
        // 测试null值（单聊）
        $this->entity->setRoomId(null);
        $this->assertNull($this->entity->getRoomId());
    }

    public function test_msgType_getter_and_setter(): void
    {
        $this->assertNull($this->entity->getMsgType());
        
        $this->entity->setMsgType('text');
        $this->assertSame('text', $this->entity->getMsgType());
        
        $this->entity->setMsgType('image');
        $this->assertSame('image', $this->entity->getMsgType());
        
        $this->entity->setMsgType('voice');
        $this->assertSame('voice', $this->entity->getMsgType());
        
        $this->entity->setMsgType('video');
        $this->assertSame('video', $this->entity->getMsgType());
        
        // 测试null值
        $this->entity->setMsgType(null);
        $this->assertNull($this->entity->getMsgType());
    }

    public function test_content_getter_and_setter(): void
    {
        $this->assertSame([], $this->entity->getContent());
        
        $content = [
            'content' => 'Hello World',
            'type' => 'text',
        ];
        
        $this->entity->setContent($content);
        $this->assertSame($content, $this->entity->getContent());
        
        // 测试复杂内容（媒体文件）
        $mediaContent = [
            'sdkfileid' => 'abc123def456',
            'filename' => 'image.png',
            'fileKey' => 'archive/2023/11/15/abc123def456.png',
        ];
        
        $this->entity->setContent($mediaContent);
        $this->assertSame($mediaContent, $this->entity->getContent());
        
        // 测试空数组
        $this->entity->setContent([]);
        $this->assertSame([], $this->entity->getContent());
    }

    public function test_corp_getter_and_setter(): void
    {
        $this->assertNull($this->entity->getCorp());        $corp = $this->createMock(CorpInterface::class);
        $this->entity->setCorp($corp);
        $this->assertSame($corp, $this->entity->getCorp());
        
        // 测试null值
        $this->entity->setCorp(null);
        $this->assertNull($this->entity->getCorp());
    }

    public function test_fluent_interface(): void
    {        $corp = $this->createMock(CorpInterface::class);
        $time = new \DateTimeImmutable();
        
        $result = $this->entity
            ->setMsgId('test_msg_id')
            ->setAction('send')
            ->setFromUserId('user123')
            ->setToList(['user1', 'user2'])
            ->setMsgTime($time)
            ->setSeq(10)
            ->setRoomId('room123')
            ->setMsgType('text')
            ->setContent(['content' => 'Hello'])
            ->setCorp($corp)
            ->setContext(['test' => 'data']);
        
        $this->assertSame($this->entity, $result);
        $this->assertSame('test_msg_id', $this->entity->getMsgId());
        $this->assertSame('send', $this->entity->getAction());
        $this->assertSame('user123', $this->entity->getFromUserId());
        $this->assertSame(['user1', 'user2'], $this->entity->getToList());
        $this->assertSame($time, $this->entity->getMsgTime());
        $this->assertSame(10, $this->entity->getSeq());
        $this->assertSame('room123', $this->entity->getRoomId());
        $this->assertSame('text', $this->entity->getMsgType());
        $this->assertSame(['content' => 'Hello'], $this->entity->getContent());
        $this->assertSame($corp, $this->entity->getCorp());
        $this->assertSame(['test' => 'data'], $this->entity->getContext());
    }

    public function test_complete_message_scenario(): void
    {        $corp = $this->createMock(CorpInterface::class);
        $time = CarbonImmutable::createFromTimestampMs(1668250655937)->toDateTimeImmutable();
        
        // 模拟完整的消息数据
        $this->entity
            ->setMsgId('16666922367251035000_1668250656103')
            ->setAction('send')
            ->setFromUserId('felix_ye')
            ->setToList(['user1@corp', 'user2@corp'])
            ->setMsgTime($time)
            ->setSeq(16)
            ->setRoomId('room_group_chat')
            ->setMsgType('image')
            ->setContent([
                'sdkfileid' => 'abc123def456ghi789',
                'filename' => 'screenshot.png',
                'fileKey' => 'archive/2023/11/15/abc123def456ghi789.png',
                'size' => 1024000,
            ])
            ->setCorp($corp)
            ->setContext([
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
        $this->assertSame('16666922367251035000_1668250656103', $this->entity->getMsgId());
        $this->assertSame('send', $this->entity->getAction());
        $this->assertSame('felix_ye', $this->entity->getFromUserId());
        $this->assertSame(['user1@corp', 'user2@corp'], $this->entity->getToList());
        $this->assertEquals($time, $this->entity->getMsgTime());
        $this->assertSame(16, $this->entity->getSeq());
        $this->assertSame('room_group_chat', $this->entity->getRoomId());
        $this->assertSame('image', $this->entity->getMsgType());
        $this->assertArrayHasKey('fileKey', $this->entity->getContent());
        $this->assertSame($corp, $this->entity->getCorp());
        $this->assertArrayHasKey('msgid', $this->entity->getContext());
    }
} 