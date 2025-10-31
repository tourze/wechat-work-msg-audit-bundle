<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Tests\Factory;

use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkMsgAuditBundle\Factory\FilesystemFactory;

/**
 * @internal
 */
#[CoversClass(FilesystemFactory::class)]
final class FilesystemFactoryTest extends TestCase
{
    private FilesystemFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FilesystemFactory();
    }

    public function testCreateFilesystemWithTestEnvironment(): void
    {
        $filesystem = $this->factory->createFilesystem('test');

        $this->assertInstanceOf(FilesystemOperator::class, $filesystem);
    }

    public function testCreateFilesystemWithProductionEnvironment(): void
    {
        $filesystem = $this->factory->createFilesystem('prod');

        $this->assertInstanceOf(FilesystemOperator::class, $filesystem);
    }

    public function testCreateFilesystemWithNullEnvironment(): void
    {
        $filesystem = $this->factory->createFilesystem();

        $this->assertInstanceOf(FilesystemOperator::class, $filesystem);
    }

    public function testTestFilesystemAdapterWriteAndRead(): void
    {
        $filesystem = $this->factory->createFilesystem('test');

        $testContent = 'test content';
        $testPath = 'test/file.txt';

        $filesystem->write($testPath, $testContent);

        $this->assertTrue($filesystem->fileExists($testPath));
        $this->assertEquals($testContent, $filesystem->read($testPath));
    }

    public function testTestFilesystemAdapterDelete(): void
    {
        $filesystem = $this->factory->createFilesystem('test');

        $testPath = 'test/file.txt';
        $filesystem->write($testPath, 'content');

        $this->assertTrue($filesystem->fileExists($testPath));

        $filesystem->delete($testPath);

        $this->assertFalse($filesystem->fileExists($testPath));
    }
}
