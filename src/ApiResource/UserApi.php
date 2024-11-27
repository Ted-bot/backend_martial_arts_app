<?php

namespace App\ApiResource;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\ApiResource\ProductApi;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\ShopOrderApi;
use App\ApiResource\UserAddressApi;
use App\ApiResource\UserProfileApi;
use App\ApiResource\SubscriptionApi;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;


#[ApiResource(
    shortName: 'User',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationItemsPerPage: 10,
    stateOptions: new Options(entityClass: User::class),
    operations: [
        new Get(    
            security: 'is_granted("ROLE_USER_STUDENT")',     
            uriTemplate: '/user_by_email/{email}/email',
            uriVariables: 'email'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_USER_SIFU")',     
        ),
        new Post(
            security: 'is_granted("PUBLIC_ACCESS")',
        ),
        new Patch(   
            security: 'is_granted("ROLE_USER_SIFU")',  
            uriTemplate: '/user_by_email/{email}/email',
            uriVariables: 'email'
        ),
        // new Patch(),
        new Put(
            security: 'is_granted("ROLE_USER_SIFU")',  
            uriTemplate: '/user_by_email/{email}/email',
            uriVariables: 'email'
        ),
        new Delete(
            security: 'is_granted("ROLE_USER_SIFU")', 
        ),
    ],
    normalizationContext: [
        'groups' => ['user:read']
    ],
    denormalizationContext: [
        'groups' => ['user:write']
    ],
)]
final class UserApi 
{
    public function __construct()
    {
        $this->userProfile = new UserProfileApi();
        $this->products = new ArrayCollection();
        $this->userAddresses = new ArrayCollection();
        $this->shopOrders = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
    }

    #[Groups(['user:read', 'profile:read'])]
    public ?int $id = null;
    
    #[Groups(['user:read', 'user:write'])]
    public ?string $firstName = null;

    #[Groups(['user:read', 'user:write'])]
    public ?string $lastName = null;
    
    #[Groups(['user:read', 'user:write'])]
    public ?string $email = null;

    #[ApiProperty(readable: false)]
    #[Groups(['user:write'])]
    public ?string $password = "";
    
    #[Groups(['user:read', 'user:write'])]
    public ?array $roles = null;

    #[Groups(['user:read', 'user:write'])]
    public ?string $libReactCity = null;

    #[Groups(['user:read', 'user:write'])]
    public ?string $libReactState = null;

    #[Groups(['user:read', 'user:write'])]
    public ?string $location = null;

    #[Groups(['user:read'])]
    /** @var DateTimeImmutable */
    public $createdAt;

    #[Groups(['user:read', 'user:write'])]
    public ?string $conversion = null;
  
    #[Groups(['user:read', 'user:write'])]
    public ?string $phoneNumber = null;

    #[Groups(['user:read', 'user:write'])]
    public ?string $dateOfBirth = null;

    #[Groups(['user:read', 'user:write'])]
    public ?string $gender = null;

    // #[ApiProperty(uriTemplate: '/api/profile/{id}')]
    /**  @var UserProfileApi $userProfile Object  */
    #[Groups(['user:read', 'user:write'])]
    public $userProfile;

    #[Groups(['user:read'])]
    /** @var array<int, ProductApi > */
    public $products;
    
    /** @var ProductApi Object > */
    public $product;

    /** @var array<int, UserAddressApi > */    
    public $userAddresses;
    
    // /** @var UserAddressApi Object */  
    #[Groups(['user:read'])]  
    public $userAddress;
    
    /** @var array<int, ShopOrderApi> */    
    #[Groups(['user:read'])]
    public $shopOrders;
    
    /** @var ShopOrderApi Object */    
    public $shopOrder;

    /** @var array<int, SubscriptionApi> > */
    #[Groups(['user:read'])]
    public $subscriptions;
    
    /** @var SubscriptionApi Object */
    public $subscription;

    // public function getId()
    // {
    //     return $this->id;
    // }

    // public function setId(int $id): void
    // {
    //     $this->id = $id;
    // }
}