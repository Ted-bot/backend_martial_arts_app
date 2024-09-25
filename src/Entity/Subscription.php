<?php

namespace App\Entity;

use ApiPlatform\Metadata\Link;
use DateTime;
use DateTimeZone;
use DateTimeInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\SubscriptionTypeEnum;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\MolliePaymentStatusEnum;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\SubscriptionLengthTypeEnum;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ApiResource(    
    shortName: 'Subscription',
    filters: ['app.subscription.property_filter'],
    description: 'Subscriptions of Users',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
    ],
    normalizationContext: [
        'groups' => ['subscription:read']
    ],
    denormalizationContext: [
        'groups' => ['subscription:write']
    ],    
)]
#[ApiResource(
    uriTemplate: '/users/{email}/subscriptions/{status}.{_format}',
    shortName: 'Subscription',
    operations: [new Get()],
    uriVariables: [
        'email' => new Link(
            identifiers: ['email'],
            fromProperty: 'subscriptions',
            fromClass: User::class
        ),
        'status' => new Link(
            identifiers: ['status']
        ),        
    ],
    normalizationContext: [
        'groups' => ['subscription:read']
    ],
)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // #[ApiProperty(identifier: false)]
    // #[ORM\Column(type: 'uuid', unique: true)]
    // #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['subscription:read'])]
    private ?int $id;
    
    #[ORM\Column(type: 'uuid', unique:true)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column(enumType: MolliePaymentStatusEnum::class, length: 255)]
    #[Groups(['subscription:read', 'user:read'])]
    private ?MolliePaymentStatusEnum $status;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['subscription:read'])]
    private ?string $amount;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups(['subscription:read', 'user:read'])]
    private ?string $transferId = null;

    #[ORM\ManyToOne(inversedBy: 'relatedSubscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Column(enumType: SubscriptionTypeEnum::class)]
    #[Groups(['subscription:read', 'user:read'])]
    private ?SubscriptionLengthTypeEnum $duration;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['subscription:read', 'user:read'])]
    private ?DateTimeInterface $dateStart;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['subscription:read', 'user:read'])]
    private ?DateTimeInterface $dateEnd;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['subscription:read', 'user:read'])]
    private ?DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['subscription:read', 'user:read'])]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    public ?User $subOwnedBy = null;

    public function __construct()
    {
        $dateTime = new DateTime('now',new DateTimeZone('Europe/Amsterdam'));
        $this->createdAt = $dateTime;
        $this->dateStart = DateTime::createFromFormat('d-m-Y',$dateTime->format('d-m-Y'),new DateTimeZone('Europe/Amsterdam'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?MolliePaymentStatusEnum
    {
        return $this->status;
    }

    public function setStatus(MolliePaymentStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTransferId(): ?string
    {
        return $this->transferId;
    }

    public function setTransferId(?string $transferId): static
    {
        $this->transferId = $transferId;

        return $this;
    }

    public function getDuration(): ?SubscriptionLengthTypeEnum
    {
        return $this->duration;
    }

    public function setDuration(SubscriptionLengthTypeEnum $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(string $dateEnd): static
    {
        $dateTime = new DateTime('now',new DateTimeZone('Europe/Amsterdam'));
        $test = DateTime::createFromFormat('d-m-Y',$dateTime->format('d-m-Y'),new DateTimeZone('Europe/Amsterdam'));
        $this->dateEnd = $test->modify($dateEnd);
        // $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt()
    {
        $dateTime = new DateTime('now',new DateTimeZone('Europe/Amsterdam'));
        $this->updatedAt = $dateTime;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getSubscriptionOwnedBy(): ?User
    {
        return $this->subOwnedBy;
    }

    public function setSubscriptionOwnedBy(?User $subOwnedBy): static
    {
        $this->subOwnedBy = $subOwnedBy;

        return $this;
    }

}
