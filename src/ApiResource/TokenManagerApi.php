<?php

namespace App\ApiResource;

use DateTimeImmutable;
use App\Entity\TokenManager;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\UserProfileApi;
use App\ApiResource\SubscriptionApi;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationClientItemsPerPage: true,
    // paginationItemsPerPage: 10,
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: TokenManager::class),
    operations: [
            new Get(
                security: 'is_granted("ROLE_USER_STUDENT")',
            ),
            new GetCollection(
                security: 'is_granted("ROLE_USER_STUDENT")',
            ),
            new Post(
                security: 'is_granted("ROLE_USER_STUDENT")',
            ),
            new Patch(
                security: 'is_granted("ROLE_USER_STUDENT")',                
                // validationContext:['groups' => ['Default', 'postValidation']]
            ),
            new Delete(),
        ],
)]
class TokenManagerApi 
{
    public function __construct()
    {
        $this->userProfile = new UserProfileApi();
        $this->relatedSubscription = new SubscriptionApi();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }
                    
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    
    /** @var Uuid $uuid */
    public $uuid = null;
    
    public ?int $tokens = null;
    
   /** @var UserProfileApi $userProfile */ 
    public $userProfile;
     
    /** @var SubscriptionApi $relatedSubscription */ 
    public $relatedSubscription;
     
    /** @var DateTimeImmutable $createdAt */ 
    public $createdAt;
     
    /** @var DateTimeImmutable $updatedAt */ 
    public $updatedAt;
}