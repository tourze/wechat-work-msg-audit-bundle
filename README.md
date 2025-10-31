# WeChat Work Message Audit Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-work-msg-audit-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-msg-audit-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-msg-audit-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-msg-audit-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-work-msg-audit-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-msg-audit-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-work-msg-audit-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-msg-audit-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

A Symfony bundle for WeChat Work (企业微信) conversation content archiving functionality.

## Features

- Automatic synchronization of archived messages from WeChat Work
- Support for various message types (text, image, voice, video, etc.)
- Media file download and storage
- Message querying and management
- Integration with WeChat Work Finance SDK
- Cron job support for automatic message synchronization

## Installation

```bash
composer require tourze/wechat-work-msg-audit-bundle
```

## Configuration

This bundle requires the following configuration:

1. **WeChat Work Corp Configuration**: Set up your corporation and agent with the message 
   archive application (Special Agent ID: 1000022)
2. **Private Key**: Configure the private key for message decryption in your agent 
   settings
3. **Storage**: Configure filesystem storage for media files

## Usage

### Console Commands

#### Sync Archive Messages

Synchronize archived messages from WeChat Work:

```bash
# Sync all messages for all configured corporations
php bin/console wechat-work:sync-archive-message

# Sync messages for a specific corporation
php bin/console wechat-work:sync-archive-message <corpId>

# Sync messages for a specific agent
php bin/console wechat-work:sync-archive-message <corpId> <agentId>
```

This command:
- Fetches new messages from WeChat Work archive API
- Downloads media files (images, voice, video) automatically
- Stores messages in the database
- Can be configured to run as a cron job (runs every minute by default)

#### Test Commands

For testing purposes, the bundle includes test commands:

```bash
# Simple test command for integration testing
php bin/console test:simple
```

This command is used internally for testing CommandTester integration.

## API Requests

### Get Permit User List

Retrieve the list of users who have enabled message archiving:

```php
use WechatWorkMsgAuditBundle\Request\GetPermitUserListRequest;

$request = new GetPermitUserListRequest();
$request->setAgent($agent);
$request->setType(1); // 1: Office edition, 2: Service edition, 3: Enterprise edition

$response = $httpClient->sendRequest($request);
```

## Entities

### ArchiveMessage

The main entity for storing archived messages:

```php
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

// Query messages
$messages = $archiveMessageRepository->findBy([
    'corp' => $corp,
    'fromUserId' => 'user123',
]);

// Access message data
$message = $messages[0];
$msgId = $message->getMsgId();
$action = $message->getAction(); // send, recall, or switch
$msgType = $message->getMsgType(); // text, image, voice, video, etc.
$content = $message->getContent(); // Message content array
$msgTime = $message->getMsgTime(); // Message timestamp
```

## Message Actions

The bundle supports three types of message actions:

- `send` - Regular message sending
- `recall` - Message recall
- `switch` - Enterprise switching log

## Advanced Usage

### Custom Message Processing

You can extend the message processing functionality by implementing custom handlers:

```php
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;

// Custom message processor
class CustomMessageProcessor
{
    public function processMessage(ArchiveMessage $message): void
    {
        // Your custom processing logic
        $content = $message->getContent();
        $msgType = $message->getMsgType();
        
        // Handle different message types
        switch ($msgType) {
            case 'text':
                $this->processTextMessage($content);
                break;
            case 'image':
                $this->processImageMessage($content);
                break;
            // ... other types
        }
    }
}
```

### Message Filtering

Filter messages based on specific criteria:

```php
// Filter by date range
$messages = $archiveMessageRepository->createQueryBuilder('m')
    ->where('m.msgTime >= :startDate')
    ->andWhere('m.msgTime <= :endDate')
    ->setParameter('startDate', $startDate)
    ->setParameter('endDate', $endDate)
    ->getQuery()
    ->getResult();

// Filter by message type
$textMessages = $archiveMessageRepository->findBy([
    'msgType' => 'text',
    'corp' => $corp
]);
```

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 3.0 or higher
- WeChat Work Finance SDK

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Reference

- [WeChat Work Message Archive API Documentation](https://developer.work.weixin.qq.com/document/path/91360)
- [WeChat Work Finance SDK](https://github.com/mochat-cloud/wework-finance-sdk-php)