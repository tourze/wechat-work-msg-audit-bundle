<?php

namespace WechatWorkMsgAuditBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkBundle\Entity\Corp;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

/**
 * @internal
 */
#[CoversClass(ArchiveMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class ArchiveMessageRepositoryTest extends AbstractRepositoryTestCase
{
    protected function getEntityClass(): string
    {
        return ArchiveMessage::class;
    }

    protected function getRepository(): ArchiveMessageRepository
    {
        return self::getService(ArchiveMessageRepository::class);
    }

    protected function createNewEntity(): ArchiveMessage
    {
        $corp = new Corp();
        $corp->setCorpId('test-corp-' . uniqid());
        $corp->setName('Test Corp ' . uniqid());
        $corp->setCorpSecret('test-secret-' . uniqid());

        // 保存 Corp 实体以确保它有 ID
        $entityManager = self::getEntityManager();
        $entityManager->persist($corp);
        $entityManager->flush();

        $entity = new ArchiveMessage();
        $entity->setCorp($corp);
        $entity->setMsgId('test-msg-' . uniqid());
        $entity->setAction('send');
        $entity->setFromUserId('test-user');
        $entity->setToList(['target-user']);
        $entity->setMsgTime(new \DateTimeImmutable());
        $entity->setSeq(1);
        $entity->setMsgType('text');

        return $entity;
    }

    protected function onSetUp(): void
    {
        // Repository 测试不需要特殊的设置
    }

    public function testRepositoryBehavior(): void
    {
        $repository = $this->getRepository();

        // 测试Repository的基本行为而非反射
        $this->assertInstanceOf(ArchiveMessageRepository::class, $repository);

        // 测试可以执行基本查询
        $allEntities = $repository->findAll();
        $this->assertIsArray($allEntities);

        // 测试可以使用条件查询
        $oneEntity = $repository->findOneBy([]);
        $this->assertTrue(null === $oneEntity || $oneEntity instanceof ArchiveMessage);
    }

    public function testRepositoryPersistenceOperations(): void
    {
        $repository = $this->getRepository();
        $entity = $this->createNewEntity();

        // 测试保存操作
        $repository->save($entity);
        $this->assertNotNull($entity->getId());

        // 测试查找已保存的实体
        $foundEntity = $repository->find($entity->getId());
        $this->assertInstanceOf(ArchiveMessage::class, $foundEntity);
        $this->assertSame($entity->getMsgId(), $foundEntity->getMsgId());

        // 保存 ID 用于删除后的查询
        $entityId = $entity->getId();

        // 测试删除操作
        $repository->remove($entity);
        $deletedEntity = $repository->find($entityId);
        $this->assertNull($deletedEntity);
    }
}
