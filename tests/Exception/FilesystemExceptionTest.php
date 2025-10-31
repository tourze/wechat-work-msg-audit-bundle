<?php

namespace WechatWorkMsgAuditBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkMsgAuditBundle\Exception\FilesystemException;

/**
 * @internal
 */
#[CoversClass(FilesystemException::class)]
class FilesystemExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCanBeCreated(): void
    {
        $message = 'Test exception message';
        $code = 123;
        $previous = new \Exception('Previous exception');

        $exception = new FilesystemException($message, $code, $previous);

        $this->assertInstanceOf(FilesystemException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionWithDefaults(): void
    {
        $exception = new FilesystemException();

        $this->assertInstanceOf(FilesystemException::class, $exception);
        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testExceptionInheritance(): void
    {
        $exception = new FilesystemException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }
}
