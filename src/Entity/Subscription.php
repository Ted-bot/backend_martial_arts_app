<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
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
    // #[ApiProperty(identifier: false)]
    // #[ORM\Column(type: 'uuid', unique: true)]
    // #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?int $id;
    
    // note: set restriction for admin
    #[ORM\Column(type: 'uuid', unique:true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['tokenmanager:read', 'profile:read', 'user:read'])]
    private ?Uuid $uuid = null;

    #[ORM\Column(enumType: MolliePaymentStatusEnum::class, length: 255)]
    #[Groups(['subscription:read', 'user:read', 'tokenmanager:read', 'profile:read'])]
    private ?MolliePaymentStatusEnum $status = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['subscription:read', 'profile:read', 'user:read'])]
    // note: set restriction only accessable by admin
    private ?string $amount = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups(['subscription:read', 'user:read'])]
    private ?string $transferId = null;

    #[ORM\ManyToOne(inversedBy: 'relatedSubscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Column(enumType: SubscriptionTypeEnum::class)]
    #[Groups(['subscription:read', 'user:read', 'tokenmanager:read', 'profile:read'])]
    private ?SubscriptionLengthTypeEnum $duration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['subscription:read', 'user:read','tokenmanager:read', 'profile:read'])]
    private ?DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['subscription:read', 'user:read', 'tokenmanager:read', 'profile:read'])]
    private ?DateTimeInterface $dateEnd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['subscription:read', 'user:read', 'tokenmanager:read', 'profile:read'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['subscription:read', 'user:read'])]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]   
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read'])]    // note: set restriction 
    private ?User $subscriptionOwnedBy = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')] // , fetch: "EAGER"
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['tokenmanager:read', 'profile:read', 'user:read'])]
    private ?Product $subscribedProduct = null;

    #[ORM\OneToOne(mappedBy: 'relatedSubscription', cascade: ['persist', 'remove'])]
    #[Groups(['user:read'])]
    private ?TokenManager $tokenManager = null;

    // #[ORM\ManyToOne(inversedBy: 'relatedSubscription')]
    // private ?TokenManager $tokenManager = null;

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

    #[ApiProperty(security: 'is_granted("Role_Admin")')]
    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }
    
    #[Groups(['user:read', 'subscription:read'])]
    public function getStartDate(): ?string
    {
        return Carbon::parse($this->dateStart)->format('d-m-Y');//->diffForHumans()
    }

    #[ApiProperty(security: 'is_granted("Role_Admin")')]
    public function setDateStart(DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?DateTimeInterface
    {
        return $this->dateEnd;
    }

    #[Groups(['user:read', 'subscription:read'])]
    public function getEndDate(): ?string
    {
        return Carbon::parse($this->dateEnd)->format('d-m-Y');
    }

    public function setDateEnd(string $dateEnd): static
    {
        $dateTime = new DateTime('now',new DateTimeZone('Europe/Amsterdam'));
        $test = DateTime::createFromFormat('d-m-Y',$dateTime->format('d-m-Y'),new DateTimeZone('Europe/Amsterdam'));
        $this->dateEnd = $test->modify($dateEnd);
        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        $dateTime = new DateTime('now',new DateTimeZone('Europe/Amsterdam'));
        // $this->createdAt = $dateTime;

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
        return $this->subscriptionOwnedBy;
    }

    public function setSubscriptionOwnedBy(?User $subscriptionOwnedBy): static
    {
        $this->subscriptionOwnedBy = $subscriptionOwnedBy;

        return $this;
    }

    public function getSubscribedProduct(): ?Product
    {
        return $this->subscribedProduct;
    }

    public function setSubscribedProduct(?Product $subscribedProduct): static
    {
        $this->subscribedProduct = $subscribedProduct;

        return $this;
    }

    // public function getTokenManager(): ?TokenManager
    // {
    //     return $this->tokenManager;
    // }

    // public function setTokenManager(?TokenManager $tokenManager): static
    // {
    //     $this->tokenManager = $tokenManager;

    //     return $this;
    // }

    public function getTokenManager(): ?TokenManager
    {
        return $this->tokenManager;
    }

    public function setTokenManager(?TokenManager $tokenManager): static
    {
        // unset the owning side of the relation if necessary
        if ($tokenManager === null && $this->tokenManager !== null) {
            $this->tokenManager->setRelatedSubscription(null);
        }

        // set the owning side of the relation if necessary
        if ($tokenManager !== null && $tokenManager->getRelatedSubscription() !== $this) {
            $tokenManager->setRelatedSubscription($this);
        }

        $this->tokenManager = $tokenManager;

        return $this;
    }

}
