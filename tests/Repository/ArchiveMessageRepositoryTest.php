<?php

namespace WechatWorkMsgAuditBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

class ArchiveMessageRepositoryTest extends TestCase
{
    public function test_repository_inheritance(): void
    {        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ArchiveMessageRepository($registry);
        
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $repository);
    }

    public function test_repository_class_name(): void
    {        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ArchiveMessageRepository($registry);
        
        $this->assertSame(ArchiveMessageRepository::class, get_class($repository));
    }

    public function test_repository_methods_exist(): void
    {        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ArchiveMessageRepository($registry);
        
        // 这些方法继承自父类，不需要检查
        $this->assertInstanceOf(ArchiveMessageRepository::class, $repository);
    }

    public function test_constructor_parameter_types(): void
    {
        // 测试构造函数参数类型
        $reflection = new \ReflectionClass(ArchiveMessageRepository::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        
        $registryParam = $parameters[0];
        $this->assertSame('registry', $registryParam->getName());
        $this->assertSame(ManagerRegistry::class, (string) $registryParam->getType());
    }

    public function test_repository_phpdoc_annotations(): void
    {
        // 测试Repository类的PHPDoc注释是否正确
        $reflection = new \ReflectionClass(ArchiveMessageRepository::class);
        $docComment = $reflection->getDocComment();
        $this->assertStringContainsString('@method ArchiveMessage|null find($id', $docComment);
        $this->assertStringContainsString('@method ArchiveMessage|null findOneBy(array $criteria', $docComment);
        $this->assertStringContainsString('@method ArchiveMessage[]    findAll()', $docComment);
        $this->assertStringContainsString('@method ArchiveMessage[]    findBy(array $criteria', $docComment);
    }
} 