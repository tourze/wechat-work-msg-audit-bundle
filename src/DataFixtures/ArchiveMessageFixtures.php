<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkBundle\DataFixtures\CorpFixtures;
use WechatWorkBundle\Entity\Corp;
use WechatWorkMsgAuditBundle\Entity\ArchiveMessage;
use WechatWorkMsgAuditBundle\Enum\MessageAction;

#[When(env: 'test')]
class ArchiveMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public const ARCHIVE_MESSAGE_1_REFERENCE = 'archive-message-1';
    public const ARCHIVE_MESSAGE_2_REFERENCE = 'archive-message-2';

    public function load(ObjectManager $manager): void
    {
        // 获取已存在的测试企业
        $corp = $this->getReference(CorpFixtures::CORP_1_REFERENCE, Corp::class);

        // 创建文本消息
        $textMessage = new ArchiveMessage();
        $textMessage->setCorp($corp);
        $textMessage->setMsgId('test_msg_001');
        $textMessage->setAction(MessageAction::SEND->value);
        $textMessage->setFromUserId('user001');
        $textMessage->setToList(['user002', 'user003']);
        $textMessage->setMsgTime(new \DateTimeImmutable('2023-01-01 10:00:00'));
        $textMessage->setSeq(1001);
        $textMessage->setMsgType('text');
        $textMessage->setContent([
            'content' => '这是一条测试消息',
        ]);
        $textMessage->setContext([
            'source' => 'test',
            'environment' => 'development',
        ]);

        $manager->persist($textMessage);
        $this->addReference(self::ARCHIVE_MESSAGE_1_REFERENCE, $textMessage);

        // 创建群聊消息
        $groupMessage = new ArchiveMessage();
        $groupMessage->setCorp($corp);
        $groupMessage->setMsgId('test_msg_002');
        $groupMessage->setAction(MessageAction::SEND->value);
        $groupMessage->setFromUserId('user001');
        $groupMessage->setToList(['group001']);
        $groupMessage->setMsgTime(new \DateTimeImmutable('2023-01-01 11:00:00'));
        $groupMessage->setSeq(1002);
        $groupMessage->setRoomId('room001');
        $groupMessage->setMsgType('image');
        $groupMessage->setContent([
            'url' => 'https://images.unsplash.com/photo-1494790108755-2616b612b593?w=300&h=200',
            'md5sum' => 'abc123def456',
            'filesize' => 102400,
        ]);
        $groupMessage->setContext([
            'source' => 'test',
            'room_type' => 'group',
        ]);

        $manager->persist($groupMessage);
        $this->addReference(self::ARCHIVE_MESSAGE_2_REFERENCE, $groupMessage);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CorpFixtures::class,
        ];
    }
}
