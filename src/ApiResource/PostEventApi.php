<?php

namespace App\ApiResource;

use DateTime;
use App\Entity\PostEvent;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use App\ApiResource\UserProfileApi;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;


#[ApiResource(
    shortName: 'trainingsession',
    description: 'Calendar Post Entity',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationClientItemsPerPage: true,
    // paginationItemsPerPage: 10,
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: PostEvent::class),
    operations: [
        new Get(),
        new GetCollection(
            filters: ['api_platform.doctrine.orm.date_filter','api_platform.doctrine.orm.order_filter', 'api_platform.doctrine.orm.boolean_filter', 'api_platform.doctrine.orm.search_filter'] //  'api_platform.doctrine.orm.boolean_filter',
        ),
        new Post(
            security: 'is_granted("ROLE_USER_SIFU")',
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_SIFU")',
        ),
        new Put(
            security: 'is_granted("ROLE_USER_SIFU")',
        ),
        new Delete(
            security: 'is_granted("ROLE_USER_SIFU")',
        ),
    ],
    normalizationContext: [
        'groups' => ['trainingsession:read']
    ],
    denormalizationContext: [
        'groups' => ['trainingsession:write']
    ],
)]
#[ApiFilter(OrderFilter::class, properties: ['id'])] //
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial', 'description' => 'partial', 'relatedUser' => 'partial'])] // #[QueryParameter(key: ':firstName', filter: SearchFilter::class)]
#[ApiFilter(BooleanFilter::class, properties: ['isPublished', 'allDay'])]
#[ApiFilter(DateFilter::class, properties: ['startDate' => 'partial','endDate' => 'partial', 'createdAt' => 'exact'] )] //,strategy: DateFilterInterface::EXCLUDE_NULL
class PostEventApi 
{
    public function __construct()
    {
        $this->subscribedTo = new ArrayCollection();
    }

    #[Groups(['trainingsession:read', 'profile:read','user:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[ApiProperty(identifier:true)]
    public ?int $id = null;

    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]    
    public ?string $title = "";

    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]    
    public ?string $description = "";

    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public ?bool $isPublished = true;

    /** @var UserProfileApi $relatedUser */
    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public $relatedUser = null;
    
    /** @var DateTime $createdAt */
    #[Groups(['trainingsession:read', 'profile:read'])]
    public $createdAt = null;
    
    /** @var DateTime $startDate */
    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public $startDate = null;
    
    /** @var DateTime $endDate */
    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public $endDate = null;

    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public ?bool $allDay = false;
    
    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    /**  @var array<int, UserProfileApi> */
    public $subscribedTo;

    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public ?UserProfileApi $subscribedBy = null;

}