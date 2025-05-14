<?php

namespace WechatWorkMsgAuditBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

class ArchiveMessageRepositoryTest extends TestCase
{
    public function testConstructor(): void
    {
        // 创建模拟对象
        $registry = $this->createMock(ManagerRegistry::class);
        
        // 创建仓库实例
        $repository = new ArchiveMessageRepository($registry);
        
        // 验证仓库类型
        $this->assertInstanceOf(ArchiveMessageRepository::class, $repository);
    }
    
    public function testRepositoryMethods(): void
    {
        // 跳过实际测试方法调用，只验证方法存在
        $repository = $this->getMockBuilder(ArchiveMessageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        // 确保继承的方法可用
        $this->assertTrue(method_exists($repository, 'find'));
        $this->assertTrue(method_exists($repository, 'findAll'));
        $this->assertTrue(method_exists($repository, 'findBy'));
        $this->assertTrue(method_exists($repository, 'findOneBy'));
    }
} 