# 企业微信会话内容存档包

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-work-msg-audit-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-msg-audit-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-msg-audit-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-msg-audit-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-work-msg-audit-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-msg-audit-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-work-msg-audit-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-msg-audit-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

一个用于企业微信会话内容存档功能的 Symfony 包。

## 功能特性

- 自动同步企业微信的归档消息
- 支持多种消息类型（文本、图片、语音、视频等）
- 媒体文件自动下载和存储
- 消息查询和管理
- 集成企业微信 Finance SDK
- 支持定时任务自动同步消息

## 安装

```bash
composer require tourze/wechat-work-msg-audit-bundle
```

## 配置

本包需要以下配置：

1. **企业微信企业配置**：设置您的企业和应用，需要配置消息存档应用（特殊应用 ID：1000022）
2. **私钥配置**：在应用设置中配置用于消息解密的私钥
3. **存储配置**：配置用于存储媒体文件的文件系统

## 使用方法

### 控制台命令

#### 同步归档消息

从企业微信同步归档消息：

```bash
# 同步所有已配置企业的所有消息
php bin/console wechat-work:sync-archive-message

# 同步特定企业的消息
php bin/console wechat-work:sync-archive-message <企业ID>

# 同步特定应用的消息
php bin/console wechat-work:sync-archive-message <企业ID> <应用ID>
```

此命令功能：
- 从企业微信归档 API 获取新消息
- 自动下载媒体文件（图片、语音、视频）
- 将消息存储到数据库
- 可配置为定时任务（默认每分钟运行一次）

#### 测试命令

用于测试目的，本包包含测试命令：

```bash
# 用于集成测试的简单测试命令
php bin/console test:simple
```

此命令内部用于测试 CommandTester 集成。

## API 请求

### 获取会话内容存档开启成员列表

获取已开启消息归档的用户列表：

```php
use WechatWorkMsgAuditBundle\Request\GetPermitUserListRequest;

$request = new GetPermitUserListRequest();
$request->setAgent($agent);
$request->setType(1); // 1: 办公版, 2: 服务版, 3: 企业版

$response = $httpClient->sendRequest($request);
```

## 实体

### ArchiveMessage

存储归档消息的主要实体：

```php
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

// 查询消息
$messages = $archiveMessageRepository->findBy([
    'corp' => $corp,
    'fromUserId' => 'user123',
]);

// 访问消息数据
$message = $messages[0];
$msgId = $message->getMsgId();        // 消息唯一标识
$action = $message->getAction();       // send、recall 或 switch
$msgType = $message->getMsgType();     // text、image、voice、video 等
$content = $message->getContent();     // 消息内容数组
$msgTime = $message->getMsgTime();     // 消息发送时间
```

## 消息动作类型

本包支持三种消息动作类型：

- `send` - 发送消息
- `recall` - 撤回消息
- `switch` - 切换企业日志

## 高级用法

### 自定义消息处理

您可以通过实现自定义处理器来扩展消息处理功能：

```php
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

// 自定义消息处理器
class CustomMessageProcessor
{
    public function processMessage(ArchiveMessage $message): void
    {
        // 您的自定义处理逻辑
        $content = $message->getContent();
        $msgType = $message->getMsgType();
        
        // 处理不同的消息类型
        switch ($msgType) {
            case 'text':
                $this->processTextMessage($content);
                break;
            case 'image':
                $this->processImageMessage($content);
                break;
            // ... 其他类型
        }
    }
}
```

### 消息过滤

根据特定条件过滤消息：

```php
// 按日期范围过滤
$messages = $archiveMessageRepository->createQueryBuilder('m')
    ->where('m.msgTime >= :startDate')
    ->andWhere('m.msgTime <= :endDate')
    ->setParameter('startDate', $startDate)
    ->setParameter('endDate', $endDate)
    ->getQuery()
    ->getResult();

// 按消息类型过滤
$textMessages = $archiveMessageRepository->findBy([
    'msgType' => 'text',
    'corp' => $corp
]);
```

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 3.0 或更高版本
- 企业微信 Finance SDK

## 贡献

详情请查看 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 许可证

MIT 许可证 (MIT)。详情请查看 [许可证文件](LICENSE)。

## 参考文档

- [企业微信会话内容存档 API 文档](https://developer.work.weixin.qq.com/document/path/91360)
- [企业微信 Finance SDK](https://github.com/mochat-cloud/wework-finance-sdk-php)