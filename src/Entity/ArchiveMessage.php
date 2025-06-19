<?php

namespace WechatWorkMsgAuditBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

#[ORM\Entity(repositoryClass: ArchiveMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_archive_message', options: ['comment' => '归档消息'])]
class ArchiveMessage implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[ORM\Column(length: 120, unique: true, options: ['comment' => '消息的唯一标识'])]
    private ?string $msgId = null;

    #[ORM\Column(length: 20, options: ['comment' => '消息动作'])]
    private ?string $action = null;

    /**
     * @var string|null 同一企业内容为userid，非相同企业为external_userid。消息如果是机器人发出，也为external_userid
     */
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '消息发送方id'])]
    private ?string $fromUserId = null;

    /**
     * @var array 可能是多个，同一个企业内容为userid，非相同企业为external_userid。数组，内容为string类型
     */
    #[ORM\Column(nullable: true, options: ['comment' => '消息接收方列表'])]
    private ?array $toList = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '消息发送时间'])]
    private ?\DateTimeInterface $msgTime = null;

    #[ORM\Column]
    private ?int $seq = null;

    /**
     * @var string|null 如果是单聊则为空
     */
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '群聊消息的群id'])]
    private ?string $roomId = null;

    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '消息类型'])]
    private ?string $msgType = null;

    #[ORM\Column(nullable: true, options: ['comment' => '消息内容'])]
    private ?array $content = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(?array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getMsgId(): ?string
    {
        return $this->msgId;
    }

    public function setMsgId(string $msgId): self
    {
        $this->msgId = $msgId;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getFromUserId(): ?string
    {
        return $this->fromUserId;
    }

    public function setFromUserId(?string $fromUserId): self
    {
        $this->fromUserId = $fromUserId;

        return $this;
    }

    public function getToList(): array
    {
        return $this->toList ?? [];
    }

    public function setToList(?array $toList): self
    {
        $this->toList = $toList ?? [];

        return $this;
    }

    public function getMsgTime(): ?\DateTimeInterface
    {
        return $this->msgTime;
    }

    public function setMsgTime(\DateTimeInterface $msgTime): self
    {
        $this->msgTime = $msgTime;

        return $this;
    }

    public function getSeq(): ?int
    {
        return $this->seq;
    }

    public function setSeq(int $seq): self
    {
        $this->seq = $seq;

        return $this;
    }

    public function getRoomId(): ?string
    {
        return $this->roomId;
    }

    public function setRoomId(?string $roomId): self
    {
        $this->roomId = $roomId;

        return $this;
    }

    public function getMsgType(): ?string
    {
        return $this->msgType;
    }

    public function setMsgType(?string $msgType): self
    {
        $this->msgType = $msgType;

        return $this;
    }

    public function getContent(): array
    {
        return $this->content ?? [];
    }

    public function setContent(?array $content): self
    {
        $this->content = $content ?? [];

        return $this;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): self
    {
        $this->corp = $corp;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
