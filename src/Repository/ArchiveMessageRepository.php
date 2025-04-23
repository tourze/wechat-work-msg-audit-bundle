<?php

namespace WechatWorkMsgAuditBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

/**
 * @method ArchiveMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArchiveMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArchiveMessage[]    findAll()
 * @method ArchiveMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchiveMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArchiveMessage::class);
    }
}
