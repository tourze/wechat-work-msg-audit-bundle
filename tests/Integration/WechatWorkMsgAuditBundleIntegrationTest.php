<?php

namespace WechatWorkMsgAuditBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\SkippedWithMessageException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use WechatWorkMsgAuditBundle\Command\SyncArchiveMessageCommand;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

/**
 * WechatWorkMsgAuditBundle集成测试
 * 
 * 注意: 运行此测试需要在全局项目中安装以下依赖:
 * - doctrine/doctrine-bundle
 * - symfony/framework-bundle
 */
class WechatWorkMsgAuditBundleIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    protected function setUp(): void
    {
        // 检查依赖
        $this->checkDependencies();

        // 启动内核
        self::bootKernel();
        $container = static::getContainer();

        // 获取实体管理器
        $entityManager = $container->get('doctrine.orm.entity_manager');
        assert($entityManager instanceof EntityManagerInterface);

        // 创建/更新数据库模式
        $schemaTool = new SchemaTool($entityManager);
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);
    }

    /**
     * 检查测试所需的依赖
     * 
     * @throws SkippedWithMessageException 如果依赖缺失则抛出此异常
     */
    private function checkDependencies(): void
    {
        if (!class_exists(KernelTestCase::class)) {
            $this->markTestSkipped('symfony/framework-bundle 依赖缺失');
        }

        if (!class_exists(EntityManagerInterface::class)) {
            $this->markTestSkipped('doctrine/orm 依赖缺失');
        }
    }

    /**
     * 测试服务注册
     */
    public function testServiceWiring(): void
    {
        $container = static::getContainer();
        
        // 测试仓库服务注册
        $repository = $container->get(ArchiveMessageRepository::class);
        $this->assertInstanceOf(ArchiveMessageRepository::class, $repository);
        
        // 测试命令注册
        $command = $container->get(SyncArchiveMessageCommand::class);
        $this->assertInstanceOf(SyncArchiveMessageCommand::class, $command);
    }
    
    /**
     * 测试实体注册
     */
    public function testEntityMapping(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        assert($entityManager instanceof EntityManagerInterface);
        
        // 测试实体元数据
        $metadata = $entityManager->getClassMetadata(ArchiveMessage::class);
        $this->assertEquals('wechat_work_archive_message', $metadata->getTableName());
        
        // 测试实体创建和持久化
        $message = new ArchiveMessage();
        $message->setMsgId('test_msg_id_' . uniqid());
        $message->setAction('send');
        $message->setFromUserId('test_user');
        $message->setMsgTime(new \DateTime());
        $message->setSeq(1);
        
        $entityManager->persist($message);
        $entityManager->flush();
        
        // 验证ID生成和持久化
        $this->assertNotNull($message->getId());
        
        // 从数据库加载并验证
        $entityManager->clear();
        $loadedMessage = $entityManager->getRepository(ArchiveMessage::class)
            ->findOneBy(['msgId' => $message->getMsgId()]);
            
        $this->assertNotNull($loadedMessage);
        $this->assertEquals($message->getMsgId(), $loadedMessage->getMsgId());
        $this->assertEquals($message->getAction(), $loadedMessage->getAction());
        $this->assertEquals($message->getFromUserId(), $loadedMessage->getFromUserId());
    }
} 