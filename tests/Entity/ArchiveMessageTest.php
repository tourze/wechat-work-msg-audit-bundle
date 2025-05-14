<?php

namespace WechatWorkMsgAuditBundle\Tests\Entity;

use DateTime;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Corp;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

class ArchiveMessageTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $message = new ArchiveMessage();
        
        $this->assertNull($message->getId());
        $this->assertEquals([], $message->getContext());
        $this->assertNull($message->getCorp());
        $this->assertNull($message->getMsgId());
        $this->assertNull($message->getAction());
        $this->assertNull($message->getFromUserId());
        $this->assertEquals([], $message->getToList());
        $this->assertNull($message->getMsgTime());
        $this->assertNull($message->getSeq());
        $this->assertNull($message->getRoomId());
        $this->assertNull($message->getMsgType());
        $this->assertEquals([], $message->getContent());
    }
    
    public function testIdGetterSetter(): void
    {
        $message = new ArchiveMessage();
        
        // 注意：ID是由Doctrine生成的，因此我们只能测试getter
        $this->assertNull($message->getId());
    }
    
    public function testContextGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $context = ['test' => 'value', 'nested' => ['data' => true]];
        
        $result = $message->setContext($context);
        
        $this->assertSame($message, $result);
        $this->assertEquals($context, $message->getContext());
        
        // 测试null值
        $message->setContext(null);
        $this->assertNull($message->getContext());
    }
    
    public function testCorpGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $corp = $this->createMock(Corp::class);
        
        $result = $message->setCorp($corp);
        
        $this->assertSame($message, $result);
        $this->assertSame($corp, $message->getCorp());
        
        // 测试null值
        $result = $message->setCorp(null);
        $this->assertNull($message->getCorp());
    }
    
    public function testMsgIdGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $msgId = '16666922367251035000_1668250656103';
        
        $result = $message->setMsgId($msgId);
        
        $this->assertSame($message, $result);
        $this->assertEquals($msgId, $message->getMsgId());
    }
    
    public function testActionGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $action = 'send';
        
        $result = $message->setAction($action);
        
        $this->assertSame($message, $result);
        $this->assertEquals($action, $message->getAction());
    }
    
    public function testFromUserIdGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $userId = 'user123';
        
        $result = $message->setFromUserId($userId);
        
        $this->assertSame($message, $result);
        $this->assertEquals($userId, $message->getFromUserId());
        
        // 测试null值
        $result = $message->setFromUserId(null);
        $this->assertNull($message->getFromUserId());
    }
    
    public function testToListGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $toList = ['user1', 'user2', 'user3'];
        
        $result = $message->setToList($toList);
        
        $this->assertSame($message, $result);
        $this->assertEquals($toList, $message->getToList());
        
        // 测试空数组值（不能设置为null）
        $result = $message->setToList([]);
        $this->assertEquals([], $message->getToList());
    }
    
    public function testMsgTimeGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $dateTime = new DateTime('2023-01-01 10:00:00');
        
        $result = $message->setMsgTime($dateTime);
        
        $this->assertSame($message, $result);
        $this->assertSame($dateTime, $message->getMsgTime());
    }
    
    public function testSeqGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $seq = 123;
        
        $result = $message->setSeq($seq);
        
        $this->assertSame($message, $result);
        $this->assertEquals($seq, $message->getSeq());
    }
    
    public function testRoomIdGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $roomId = 'room123';
        
        $result = $message->setRoomId($roomId);
        
        $this->assertSame($message, $result);
        $this->assertEquals($roomId, $message->getRoomId());
        
        // 测试null值
        $result = $message->setRoomId(null);
        $this->assertNull($message->getRoomId());
    }
    
    public function testMsgTypeGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $msgType = 'text';
        
        $result = $message->setMsgType($msgType);
        
        $this->assertSame($message, $result);
        $this->assertEquals($msgType, $message->getMsgType());
        
        // 测试null值
        $result = $message->setMsgType(null);
        $this->assertNull($message->getMsgType());
    }
    
    public function testContentGetterSetter(): void
    {
        $message = new ArchiveMessage();
        $content = ['text' => 'Hello world', 'mentioned' => ['user1', 'user2']];
        
        $result = $message->setContent($content);
        
        $this->assertSame($message, $result);
        $this->assertEquals($content, $message->getContent());
        
        // 测试空数组值（不能设置为null）
        $result = $message->setContent([]);
        $this->assertEquals([], $message->getContent());
    }
} 