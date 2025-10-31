<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

/**
 * @extends ServiceEntityRepository<ArchiveMessage>
 */
#[AsRepository(entityClass: ArchiveMessage::class)]
class ArchiveMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArchiveMessage::class);
    }

    public function save(ArchiveMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ArchiveMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
