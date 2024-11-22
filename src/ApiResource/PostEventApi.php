<?php

namespace App\ApiResource;

use DateTime;
use App\Entity\User;
use DateTimeImmutable;
use DateTimeInterface;
use App\Entity\PostEvent;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\Enum\CountryTypeEnum;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\ApiResource\UserProfileApi;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;


#[ApiResource(
    shortName: 'trainingsession',
    description: 'Calendar Post Entity',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationItemsPerPage: 10,
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: PostEvent::class),
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_USER_SIFU")',
            // validationContext:['groups' => ['Default', 'postValidation']],
            // denormalizationContext:['groups' => ['trainingsession:write']],
            // normalizationContext:['groups' => ['trainingsession:read']]
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
class PostEventApi 
{
    public function __construct()
    {
        $this->subscribedTo = new ArrayCollection();
    }

    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
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
    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public $createdAt = null;
    
    /** @var DateTime $startDate */
    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public $startDate = null;
    
    /** @var DateTime $endDate */
    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public $endDate = null;

    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public ?bool $allDay = null;
    
    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    /**  @var array<int, UserProfileApi> */
    public $subscribedTo;

    #[Groups(['trainingsession:read', 'profile:read','trainingsession:write'])]
    public ?UserProfileApi $subscribedBy = null;

}