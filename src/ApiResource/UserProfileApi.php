<?php

namespace App\ApiResource;

use App\Entity\UserProfile;
use App\ApiResource\UserApi;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Attribute\Groups;

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
            security: 'is_granted("ROLE_USER_SIFU")',
            filters: ['api_platform.doctrine.orm.order_filter', 'api_platform.doctrine.orm.search_filter']
        ),
        new Post(
            security: 'is_granted("ROLE_USER_STUDENT")' 
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_STUDENT")',
            // uriTemplate: '/trainingsessions/{id}',
            // uriVariables: 'id'
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
#[ApiFilter(SearchFilter::class, properties: ['userUniq' => 'exact', 'username' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['id'])]
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
    #[Groups(['profile:read', 'trainingsession:read'])]
    #[ApiProperty(identifier: true)] // readable:false, 
    public ?int $id = null;

    #[Groups(['profile:read', 'profile:write', 'trainingsession:read'])]
    public ?string $username = null;

    #[Groups(['profile:read', 'profile:write'])]
    public ?string $description = null;

    #[Groups(['profile:read', 'profile:write'])]
    public ?string $websiteUrl = null;

    #[Groups(['profile:read', 'profile:write'])]    
    /** @var UserApi $userUniq */
    public $userUniq = null;
    
    /** @var UserProfileApi Object > */    
    public $groupStudent;
    
    // /** @var array<int, PostEventAp> */  
    // #[Groups(['profile:read'])]  
    public $postEvents;
    
    /** @var PostEventApi Object */    
    public $postEvent;
    
    /** @var array<int, PostEventApi> */
    #[Groups(['profile:read', 'profile:write'])]   
    public $subscribeToEvents;
    
    /** @var PostEventApi Object */    
    public $subscribeToEvent;
    
    /** @var array<int, TokenManagerApi> */
    public $tokenManagers;
    
    /** @var TokenManagerApi Object */
    public $tokenManager;
}