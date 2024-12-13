<?php

namespace App\ApiResource;

use DateTimeInterface;
use App\ApiResource\UserApi;
use App\Entity\Subscription;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\MolliePaymentStatusEnum;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use App\Enum\SubscriptionLengthTypeEnum;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Symfony\Component\Serializer\Attribute\Groups;

// #[ApiResource(    
//     shortName: 'Subscription',
//     filters: ['app.subscription.property_filter'],
//     description: 'Subscriptions of Users',
//     operations: [
//         new Get(),
//         new GetCollection(),
//         new Post(),
//         new Put(),
//         new Patch(),
//     ],
//     normalizationContext: [
//         // 'groups' => ['subscription:read']
//     ],
//     denormalizationContext: [
//         // 'groups' => ['subscription:write']
//     ],    
// )]
// #[ApiResource(
//     uriTemplate: '/users/{email}/subscriptions/{status}.{_format}',
//     shortName: 'Subscription',
//     operations: [new Get()],
//     uriVariables: [
//         'email' => new Link(
//             identifiers: ['email'],
//             fromProperty: 'subscriptions',
//             fromClass: User::class
//         ),
//         'status' => new Link(
//             identifiers: ['status']
//         ),        
//     ],
//     normalizationContext: [
//         'groups' => ['subscription:read']
//     ],
// )]
#[ApiResource(
    shortName: 'Subscription',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationClientItemsPerPage: true,
    // paginationItemsPerPage: 10,
    stateOptions: new Options(entityClass: Subscription::class),
    
    operations: [
        new Get(),
        new Get(
            security: 'is_granted("ROLE_USER_STUDENT")',
            uriTemplate: '/users/{email}/subscriptions/{status}.{_format}',
            filters: ['app.subscription.property_filter'],
            uriVariables: [
                'email' => new Link(
                    identifiers: ['email'],
                    fromProperty: 'subscriptions',
                    fromClass: UserApi::class
                ),
                'status' => new Link(
                    identifiers: ['status']
                ),        
            ],
        ),
        new GetCollection(
            security: 'is_granted("ROLE_USER_STUDENT")',
        ),
        new Post(
            security: 'is_granted("ROLE_USER_STUDENT")',
            // validationContext:['groups' => ['Default', 'postValidation']]
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_STUDENT")',
        ),
        // new Put(),
        new Delete(
            security: 'is_granted("ROLE_USER_SIFU")',
        ),
    ],
    normalizationContext: [
        'groups' => ['subscription:read']
    ],
    denormalizationContext: [
        'groups' => ['subscription:write']
    ], 
)]
class SubscriptionApi 
{
    public function __construct()
    {
        $this->subscriptionOwnedBy = new UserApi();
    }
    
    #[Groups(['profile:read', 'subscription:read', 'user:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[ApiProperty(identifier:true)]
    public ?int $id = null;

    #[Groups(['subscription:read'])] // adding user:read shows to error
    public ?Uuid $uuid = null;

    #[Groups(['subscription:read'])] // adding user:read shows to error
    public ?MolliePaymentStatusEnum $status  = null;

    #[Groups(['subscription:read'])] // adding user:read shows to error
    public ?string $amount = null;

    #[Groups(['subscription:read'])]
    public ?string $transferId = null;
    
    /** @var SubscriptionLengthTypeEnum $duration */
    #[Groups(['subscription:read'])]
    public $duration  = null;
     
    /** @var DateTimeInterface $dateStart */
    #[Groups(['subscription:read'])]
    public $dateStart  = null;
     
    /** @var DateTimeInterface $dateEnd */
    #[Groups(['subscription:read'])]
    public $dateEnd  = null;
     
    /** @var DateTimeInterface $createdAt */
    #[Groups(['subscription:read'])]
    public $createdAt  = null;
     
    /** @var DateTimeInterface $updatedAt */
    #[Groups(['subscription:read'])]
    public $updatedAt = null;
     
    /** @var UserApi $subscriptionOwnedBy */
    #[Groups(['subscription:read'])]
    public $subscriptionOwnedBy = null;
     
    /** @var ProductApi $subscribedProduct */
    #[Groups(['subscription:read'])]
    public $subscribedProduct = null;
     

    /** @var TokenManagerApi $tokenManager */
     #[Groups(['subscription:read'])]
    public $tokenManager = null;
// public ?TokenManager $tokenManager = null;
}