<?php

namespace App\ApiResource;

use App\Entity\User;
use DateTimeImmutable;
use DateTimeInterface;
use App\Entity\Product;
use App\Entity\ShopOrder;
use App\Entity\UserAddress;
use App\Entity\UserProfile;
use App\Entity\Subscription;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\MolliePaymentStatusEnum;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use App\Enum\SubscriptionLengthTypeEnum;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotBlank;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    shortName: 'subscription',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationItemsPerPage: 10,
    // normalizationContext: ['groups' => ['read_customer']],
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: Subscription::class),
// operations: [
    //     new Get(),
    //     new GetCollection(),
    //     new Post(
    //         security: 'is_granted("PUBLIC_ACCESS")',
    //         validationContext:['groups' => ['Default', 'postValidation']]
    //     ),
    //     new Patch(),
    //     // new Put(),
    //     new Delete(),
    // ],
)]
class SubscriptionApi 
{
    public function __construct()
    {
        $this->subscriptionOwnedBy = new UserApi();
    }
    
    // #[Groups(["read_customer"])] // "write_customer", 
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    public ?Uuid $uuid = null;
    public ?MolliePaymentStatusEnum $status  = null;
    public ?string $amount = null;
    public ?string $transferId = null;
    public ?SubscriptionLengthTypeEnum $duration  = null;
    public ?DateTimeInterface $dateStart  = null;
    public ?DateTimeInterface $dateEnd  = null;
    public ?DateTimeInterface $createdAt  = null;
    public ?DateTimeInterface $updatedAt = null;
    public ?UserApi $subscriptionOwnedBy = null;
    public ?ProductApi $subscribedProduct = null;
    public ?TokenManagerApi $tokenManager = null;
// public ?TokenManager $tokenManager = null;
}