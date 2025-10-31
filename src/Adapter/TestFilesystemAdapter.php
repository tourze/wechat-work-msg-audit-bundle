<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Adapter;

use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use WechatWorkMsgAuditBundle\Exception\FilesystemException;

class TestFilesystemAdapter implements FilesystemAdapter
{
    /**
     * @var array<string, string>
     */
    private array $files = [];

    public function fileExists(string $path): bool
    {
        return isset($this->files[$path]);
    }

    public function directoryExists(string $path): bool
    {
        return true;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $this->files[$path] = $contents;
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->files[$path] = stream_get_contents($contents);
    }

    public function read(string $path): string
    {
        return $this->files[$path] ?? '';
    }

    /**
     * @return resource
     */
    public function readStream(string $path)
    {
        $content = $this->files[$path] ?? '';
        $resource = fopen('php://memory', 'r+');
        if (false === $resource) {
            throw new FilesystemException('Failed to create memory resource');
        }
        fwrite($resource, $content);
        rewind($resource);

        return $resource;
    }

    public function delete(string $path): void
    {
        unset($this->files[$path]);
    }

    public function deleteDirectory(string $path): void
    {
        foreach ($this->files as $file => $content) {
            if (str_starts_with($file, $path . '/')) {
                unset($this->files[$file]);
            }
        }
    }

    public function createDirectory(string $path, Config $config): void
    {
        // No-op for testing
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // No-op for testing
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }

    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes($path, null, null, null, 'application/octet-stream');
    }

    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes($path, null, null, time());
    }

    public function fileSize(string $path): FileAttributes
    {
        $size = strlen($this->files[$path] ?? '');

        return new FileAttributes($path, $size);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $result = [];
        $pathPrefix = '' === $path ? '' : $path . '/';

        foreach ($this->files as $filePath => $content) {
            // 检查文件是否在指定路径下
            if (!str_starts_with($filePath, $pathPrefix)) {
                continue;
            }

            // 获取相对路径
            $relativePath = substr($filePath, strlen($pathPrefix));

            // 如果不是深度遍历，跳过子目录中的文件
            if (!$deep && str_contains($relativePath, '/')) {
                continue;
            }

            $result[] = new FileAttributes($filePath);
        }

        return $result;
    }

    public function move(string $source, string $destination, Config $config): void
    {
        if (isset($this->files[$source])) {
            $this->files[$destination] = $this->files[$source];
            unset($this->files[$source]);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        if (isset($this->files[$source])) {
            $this->files[$destination] = $this->files[$source];
        }
    }
}
