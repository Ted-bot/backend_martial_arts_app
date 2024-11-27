<?php

namespace App\ApiResource;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Product;
use App\Entity\ShopOrder;
use App\Entity\UserAddress;
use App\Entity\UserProfile;
use App\ApiResource\UserApi;
use App\Entity\Subscription;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\ApiResource\GroupApi;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\ApiResource\ProductApi;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\ShopOrderApi;
use App\ApiResource\UserAddressApi;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotBlank;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    shortName: 'profile',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationItemsPerPage: 10,
    stateOptions: new Options(entityClass: UserProfile::class),
    operations: [
        new Get(
            security: 'is_granted("ROLE_USER_STUDENT")' 
        ),
        new GetCollection(
            security: 'is_granted("ROLE_USER_SIFU")' 
        ),
        new Post(
            security: 'is_granted("ROLE_USER_STUDENT")' 
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_STUDENT")' 
        ),
        new Put(
            security: 'is_granted("ROLE_USER_SIFU")'
        ),
        new Delete(
            security: 'is_granted("ROLE_USER_SIFU")' 
        ),
    ],
    normalizationContext: [
        'groups' => ['profile:read']
            ],
    denormalizationContext: [
        'groups' => ['profile:write']
    ],
)]
class UserProfileApi
{
    public function __construct()
    {
        $this->postEvents = new ArrayCollection();
        $this->tokenManagers = new ArrayCollection();
        // $this->tokenManager = new TokenManagerApi();
        $this->subscribeToEvents = new ArrayCollection();
    }

    // #[Groups(["read_customer"])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['profile:read'])]
    #[ApiProperty(identifier: true)] // readable:false, 
    public ?int $id = null;

    #[Groups(['profile:read'])]
    public ?string $username = null;

    #[Groups(['profile:read'])]
    public ?string $description = null;

    #[Groups(['profile:read'])]
    public ?string $websiteUrl = null;

    #[Groups(['profile:read'])]    
    /** @var UserApi $userUniq */
    public $userUniq = null;
    
    /** @var UserProfileApi Object > */    
    public $groupStudent;
    
    // /** @var array<int, PostEventAp> */  
    #[Groups(['profile:read'])]  
    public $postEvents;
    
    /** @var PostEventApi Object */    
    public $postEvent;
    
    /** @var array<int, PostEventApi> */
    public $subscribeToEvents;
    
    /** @var PostEventApi Object */    
    public $subscribeToEvent;
    
    /** @var array<int, TokenManagerApi> */
    public $tokenManagers;
    
    /** @var TokenManagerApi Object */
    public $tokenManager;
}