<?php

namespace WechatWorkMsgAuditBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

class ArchiveMessageRepositoryTest extends TestCase
{
    public function test_repository_inheritance(): void
    {
        /** @var ManagerRegistry $registry */
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ArchiveMessageRepository($registry);
        
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $repository);
    }

    public function test_repository_class_name(): void
    {
        /** @var ManagerRegistry $registry */
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ArchiveMessageRepository($registry);
        
        $this->assertSame(ArchiveMessageRepository::class, get_class($repository));
    }

    public function test_repository_methods_exist(): void
    {
        /** @var ManagerRegistry $registry */
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ArchiveMessageRepository($registry);
        
        // 测试继承的方法是否存在
        $this->assertTrue(method_exists($repository, 'find'));
        $this->assertTrue(method_exists($repository, 'findOneBy'));
        $this->assertTrue(method_exists($repository, 'findAll'));
        $this->assertTrue(method_exists($repository, 'findBy'));
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
        $this->assertSame(ManagerRegistry::class, $registryParam->getType()->getName());
    }

    public function test_repository_phpdoc_annotations(): void
    {
        // 测试Repository类的PHPDoc注释是否正确
        $reflection = new \ReflectionClass(ArchiveMessageRepository::class);
        $docComment = $reflection->getDocComment();
        
        $this->assertIsString($docComment);
        $this->assertStringContainsString('@method ArchiveMessage|null find($id', $docComment);
        $this->assertStringContainsString('@method ArchiveMessage|null findOneBy(array $criteria', $docComment);
        $this->assertStringContainsString('@method ArchiveMessage[]    findAll()', $docComment);
        $this->assertStringContainsString('@method ArchiveMessage[]    findBy(array $criteria', $docComment);
    }
} 