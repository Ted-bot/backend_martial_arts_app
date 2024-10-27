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
    // denormalizationContext: ['groups' => ['write_customer']],
    // normalizationContext: ['groups' => ['read_customer']],
    // security: 'is_granted("ROLE_USER_STUDENT")',
    //     normalizationContext: [
    // 'groups' => ['user:read']
    //     ],
    //     denormalizationContext: [
    // 'groups' => ['user:write']
    //     ],
    stateOptions: new Options(entityClass: User::class),
    operations: [
        new Get(    
            uriTemplate: '/user_by_email/{email}/email',
            uriVariables: 'email'
        ),
        new GetCollection(),
        new Post(
            security: 'is_granted("PUBLIC_ACCESS")',
        ),
        new Patch(    
            uriTemplate: '/user_by_email/{email}/email',
            uriVariables: 'email'
        ),
        // new Patch(),
        new Put(),
        new Delete(),
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

    public ?int $id = null;
    
    public ?string $firstName = null;

    public ?string $lastName = null;
    
    public ?string $email = null;

    #[ApiProperty(readable: false)]
    public ?string $password = null;
    
    public ?array $roles = null;

    public ?string $libReactCity = null;

    public ?string $libReactState = null;

    public ?string $location = null;

    /** @var DateTimeImmutable */
    public $createdAt;

    public ?string $conversion = null;
    
    public ?string $phoneNumber = null;

    public ?string $dateOfBirth = null;

    public ?string $gender = null;

    /**  @var UserProfileApi Object  */
    public $userProfile;

    /** @var array<int, ProductApi > */
    public $products;
    
    /** @var ProductApi Object > */
    public $product;

    /** @var array<int, UserAddressApi > */    
    public $userAddresses;
    
    // /** @var UserAddressApi Object */    
    public $userAddress;
    
    /** @var array<int, ShopOrderApi> */    
    public $shopOrders;
    
    /** @var ShopOrderApi Object */    
    public $shopOrder;

    /** @var array<int, SubscriptionApi> > */
    public $subscriptions;
    
    /** @var SubscriptionApi Object */
    public $subscription;

}