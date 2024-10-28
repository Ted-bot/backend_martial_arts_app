<?php

namespace App\ApiResource;

use DateTimeInterface;
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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    shortName: 'subscription',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationItemsPerPage: 10,
    stateOptions: new Options(entityClass: Subscription::class),
    normalizationContext: [
        'groups' => ['subscription:read']
            ],
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
    
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[ApiProperty(identifier:true)]
    public ?int $id = null;
    public ?Uuid $uuid = null;

    public ?MolliePaymentStatusEnum $status  = null;

    public ?string $amount = null;

    #[Groups(['subscription:read'])]
    public ?string $transferId = null;
    
    /** @var SubscriptionLengthTypeEnum $duration */
    public $duration  = null;
     
     /** @var DateTimeInterface $dateStart */
    public $dateStart  = null;
     
     /** @var DateTimeInterface $dateEnd */
    public $dateEnd  = null;
     
     /** @var DateTimeInterface $createdAt */
    public $createdAt  = null;
     
     /** @var DateTimeInterface $updatedAt */
    public $updatedAt = null;
     
     /** @var UserApi $subscriptionOwnedBy */
    public $subscriptionOwnedBy = null;
     
     /** @var ProductApi $subscribedProduct */
    public $subscribedProduct = null;
     
     /** @var TokenManagerApi $tokenManager */
    public $tokenManager = null;
// public ?TokenManager $tokenManager = null;
}