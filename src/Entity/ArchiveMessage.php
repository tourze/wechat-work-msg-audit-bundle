<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkMsgAuditBundle\Repository\ArchiveMessageRepository;

#[ORM\Entity(repositoryClass: ArchiveMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_archive_message', options: ['comment' => '归档消息'])]
class ArchiveMessage implements \Stringable
{
    use SnowflakeKeyAware;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, unique: true, options: ['comment' => '消息的唯一标识'])]
    private ?string $msgId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, options: ['comment' => '消息动作'])]
    private ?string $action = null;

    /**
     * @var string|null 同一企业内容为userid，非相同企业为external_userid。消息如果是机器人发出，也为external_userid
     */
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '消息发送方id'])]
    private ?string $fromUserId = null;

    /**
     * @var string[] 可能是多个，同一个企业内容为userid，非相同企业为external_userid。数组，内容为string类型
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '消息接收方列表'])]
    private array $toList = [];

    #[Assert\NotNull]
    #[Assert\Type(type: '\DateTimeImmutable')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '消息发送时间'])]
    private ?\DateTimeImmutable $msgTime = null;

    #[Assert\NotNull]
    #[Assert\Type(type: 'int')]
    #[ORM\Column(options: ['comment' => '消息序列号'])]
    private ?int $seq = null;

    /**
     * @var string|null 如果是单聊则为空
     */
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '群聊消息的群id'])]
    private ?string $roomId = null;

    #[Assert\Length(max: 40)]
    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '消息类型'])]
    private ?string $msgType = null;

    /**
     * @var array<string, mixed>
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '消息内容'])]
    private array $content = [];

    /**
     * @return array<string, mixed>|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * @param array<string, mixed>|null $context
     */
    public function setContext(?array $context): void
    {
        $this->context = $context;
    }

    public function getMsgId(): ?string
    {
        return $this->msgId;
    }

    public function setMsgId(string $msgId): void
    {
        $this->msgId = $msgId;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getFromUserId(): ?string
    {
        return $this->fromUserId;
    }

    public function setFromUserId(?string $fromUserId): void
    {
        $this->fromUserId = $fromUserId;
    }

    /**
     * @return string[]
     */
    public function getToList(): array
    {
        return $this->toList ?? [];
    }

    /**
     * @param string[]|null $toList
     */
    public function setToList(?array $toList): void
    {
        $this->toList = $toList ?? [];
    }

    public function getMsgTime(): ?\DateTimeImmutable
    {
        return $this->msgTime;
    }

    public function setMsgTime(\DateTimeImmutable $msgTime): void
    {
        $this->msgTime = $msgTime;
    }

    public function getSeq(): ?int
    {
        return $this->seq;
    }

    public function setSeq(int $seq): void
    {
        $this->seq = $seq;
    }

    public function getRoomId(): ?string
    {
        return $this->roomId;
    }

    public function setRoomId(?string $roomId): void
    {
        $this->roomId = $roomId;
    }

    public function getMsgType(): ?string
    {
        return $this->msgType;
    }

    public function setMsgType(?string $msgType): void
    {
        $this->msgType = $msgType;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContent(): array
    {
        return $this->content ?? [];
    }

    /**
     * @param array<string, mixed>|null $content
     */
    public function setContent(?array $content): void
    {
        $this->content = $content ?? [];
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): void
    {
        $this->corp = $corp;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
