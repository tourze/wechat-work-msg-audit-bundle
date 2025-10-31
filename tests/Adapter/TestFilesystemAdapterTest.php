<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Tests\Adapter;

use League\Flysystem\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkMsgAuditBundle\Adapter\TestFilesystemAdapter;

/**
 * 测试文件系统适配器测试
 * @internal
 */
#[CoversClass(TestFilesystemAdapter::class)]
class TestFilesystemAdapterTest extends TestCase
{
    private TestFilesystemAdapter $adapter;

    protected function setUp(): void
    {
        $this->adapter = new TestFilesystemAdapter();
    }

    public function testAdapterCanBeInstantiated(): void
    {
        // 如果setUp()中的实例化成功，这个测试就通过了
        $this->assertTrue(true);
    }

    public function testFileExists(): void
    {
        // 测试不存在的文件
        self::assertFalse($this->adapter->fileExists('non-existent.txt'));

        // 写入文件后应该存在
        $this->adapter->write('test.txt', 'content', new Config());
        self::assertTrue($this->adapter->fileExists('test.txt'));
    }

    public function testWrite(): void
    {
        $path = 'test.txt';
        $content = 'Hello World';
        $config = new Config();

        $this->adapter->write($path, $content, $config);

        self::assertTrue($this->adapter->fileExists($path));
        self::assertSame($content, $this->adapter->read($path));
    }

    public function testRead(): void
    {
        $path = 'test.txt';
        $content = 'Test content';

        $this->adapter->write($path, $content, new Config());
        $result = $this->adapter->read($path);

        self::assertSame($content, $result);
    }

    public function testDelete(): void
    {
        $path = 'test.txt';

        $this->adapter->write($path, 'content', new Config());
        self::assertTrue($this->adapter->fileExists($path));

        $this->adapter->delete($path);
        self::assertFalse($this->adapter->fileExists($path));
    }

    public function testDirectoryListing(): void
    {
        $this->adapter->write('dir/file1.txt', 'content1', new Config());
        $this->adapter->write('dir/file2.txt', 'content2', new Config());
        $this->adapter->write('other.txt', 'content3', new Config());

        $listing = iterator_to_array($this->adapter->listContents('dir', false));

        self::assertCount(2, $listing);
        $paths = array_map(fn ($item) => $item->path(), $listing);
        self::assertContains('dir/file1.txt', $paths);
        self::assertContains('dir/file2.txt', $paths);
    }

    public function testMove(): void
    {
        $source = 'source.txt';
        $destination = 'destination.txt';
        $content = 'Move test content';

        $this->adapter->write($source, $content, new Config());
        $this->adapter->move($source, $destination, new Config());

        self::assertFalse($this->adapter->fileExists($source));
        self::assertTrue($this->adapter->fileExists($destination));
        self::assertSame($content, $this->adapter->read($destination));
    }

    public function testCopy(): void
    {
        $source = 'source.txt';
        $destination = 'destination.txt';
        $content = 'Copy test content';

        $this->adapter->write($source, $content, new Config());
        $this->adapter->copy($source, $destination, new Config());

        self::assertTrue($this->adapter->fileExists($source));
        self::assertTrue($this->adapter->fileExists($destination));
        self::assertSame($content, $this->adapter->read($source));
        self::assertSame($content, $this->adapter->read($destination));
    }

    public function testCreateDirectory(): void
    {
        $path = 'test-directory';
        $config = new Config();

        // createDirectory 在测试适配器中是无操作，只验证不抛出异常
        $this->adapter->createDirectory($path, $config);

        // 验证方法执行成功（无异常）
        $this->assertTrue(true);
    }

    public function testDeleteDirectory(): void
    {
        // 创建目录结构
        $this->adapter->write('test-dir/file1.txt', 'content1', new Config());
        $this->adapter->write('test-dir/file2.txt', 'content2', new Config());
        $this->adapter->write('other-file.txt', 'content3', new Config());

        // 验证文件存在
        self::assertTrue($this->adapter->fileExists('test-dir/file1.txt'));
        self::assertTrue($this->adapter->fileExists('test-dir/file2.txt'));
        self::assertTrue($this->adapter->fileExists('other-file.txt'));

        // 删除目录
        $this->adapter->deleteDirectory('test-dir');

        // 验证目录下的文件被删除
        self::assertFalse($this->adapter->fileExists('test-dir/file1.txt'));
        self::assertFalse($this->adapter->fileExists('test-dir/file2.txt'));
        // 其他文件应该保留
        self::assertTrue($this->adapter->fileExists('other-file.txt'));
    }

    public function testDirectoryExists(): void
    {
        // 在测试适配器中，directoryExists 总是返回 true
        self::assertTrue($this->adapter->directoryExists('any-directory'));
        self::assertTrue($this->adapter->directoryExists('nested/directory/path'));
        self::assertTrue($this->adapter->directoryExists(''));
    }

    public function testFileSize(): void
    {
        $path = 'test-file.txt';
        $content = 'Hello World';

        $this->adapter->write($path, $content, new Config());

        $fileAttributes = $this->adapter->fileSize($path);

        self::assertSame($path, $fileAttributes->path());
        self::assertSame(strlen($content), $fileAttributes->fileSize());
    }

    public function testFileSizeForNonExistentFile(): void
    {
        $path = 'non-existent-file.txt';

        $fileAttributes = $this->adapter->fileSize($path);

        self::assertSame($path, $fileAttributes->path());
        self::assertSame(0, $fileAttributes->fileSize());
    }

    public function testLastModified(): void
    {
        $path = 'test-file.txt';
        $content = 'Test content';

        $this->adapter->write($path, $content, new Config());

        $fileAttributes = $this->adapter->lastModified($path);

        self::assertSame($path, $fileAttributes->path());
        self::assertIsInt($fileAttributes->lastModified());
        self::assertGreaterThan(0, $fileAttributes->lastModified());
    }

    public function testListContentsShallow(): void
    {
        // 创建文件结构
        $this->adapter->write('root-file.txt', 'content', new Config());
        $this->adapter->write('dir1/file1.txt', 'content1', new Config());
        $this->adapter->write('dir1/file2.txt', 'content2', new Config());
        $this->adapter->write('dir1/subdir/file3.txt', 'content3', new Config());

        // 浅层遍历
        $listing = iterator_to_array($this->adapter->listContents('dir1', false));

        self::assertCount(2, $listing);
        $paths = array_map(fn ($item) => $item->path(), $listing);
        self::assertContains('dir1/file1.txt', $paths);
        self::assertContains('dir1/file2.txt', $paths);
        self::assertNotContains('dir1/subdir/file3.txt', $paths);
    }

    public function testListContentsDeep(): void
    {
        // 创建文件结构
        $this->adapter->write('dir1/file1.txt', 'content1', new Config());
        $this->adapter->write('dir1/file2.txt', 'content2', new Config());
        $this->adapter->write('dir1/subdir/file3.txt', 'content3', new Config());

        // 深度遍历
        $listing = iterator_to_array($this->adapter->listContents('dir1', true));

        self::assertCount(3, $listing);
        $paths = array_map(fn ($item) => $item->path(), $listing);
        self::assertContains('dir1/file1.txt', $paths);
        self::assertContains('dir1/file2.txt', $paths);
        self::assertContains('dir1/subdir/file3.txt', $paths);
    }

    public function testListContentsEmptyPath(): void
    {
        // 创建一些文件
        $this->adapter->write('file1.txt', 'content1', new Config());
        $this->adapter->write('file2.txt', 'content2', new Config());

        // 列出根目录内容
        $listing = iterator_to_array($this->adapter->listContents('', false));

        self::assertCount(2, $listing);
        $paths = array_map(fn ($item) => $item->path(), $listing);
        self::assertContains('file1.txt', $paths);
        self::assertContains('file2.txt', $paths);
    }

    public function testMimeType(): void
    {
        $path = 'test-file.txt';
        $content = 'Test content';

        $this->adapter->write($path, $content, new Config());

        $fileAttributes = $this->adapter->mimeType($path);

        self::assertSame($path, $fileAttributes->path());
        self::assertSame('application/octet-stream', $fileAttributes->mimeType());
    }

    public function testReadStream(): void
    {
        $path = 'test-file.txt';
        $content = 'Stream test content';

        $this->adapter->write($path, $content, new Config());

        $stream = $this->adapter->readStream($path);

        self::assertIsResource($stream);
        $readContent = stream_get_contents($stream);
        self::assertSame($content, $readContent);

        fclose($stream);
    }

    public function testReadStreamForNonExistentFile(): void
    {
        $path = 'non-existent-file.txt';

        $stream = $this->adapter->readStream($path);

        self::assertIsResource($stream);
        $readContent = stream_get_contents($stream);
        self::assertSame('', $readContent);

        fclose($stream);
    }

    public function testVisibility(): void
    {
        $path = 'test-file.txt';
        $content = 'Test content';

        $this->adapter->write($path, $content, new Config());

        $fileAttributes = $this->adapter->visibility($path);

        self::assertSame($path, $fileAttributes->path());
        // visibility 方法返回基本的 FileAttributes，不设置具体可见性
        self::assertNull($fileAttributes->visibility());
    }

    public function testWriteStream(): void
    {
        $path = 'stream-test.txt';
        $content = 'Stream write test content';

        // 创建内存流
        $stream = fopen('php://memory', 'r+');
        self::assertIsResource($stream);
        fwrite($stream, $content);
        rewind($stream);

        $this->adapter->writeStream($path, $stream, new Config());

        // 验证文件存在且内容正确
        self::assertTrue($this->adapter->fileExists($path));
        self::assertSame($content, $this->adapter->read($path));

        fclose($stream);
    }
}
