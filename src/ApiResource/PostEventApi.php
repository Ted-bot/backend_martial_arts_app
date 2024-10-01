<?php

namespace App\ApiResource;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\PostEvent;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\Enum\CountryTypeEnum;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Doctrine\ORM\Mapping as ORM;


#[ApiResource(
    // shortName: 'address',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationItemsPerPage: 10,
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: PostEvent::class),
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
class PostEventApi 
{
    public function __construct()
    {
        $this->subscribe = new ArrayCollection();
        // $this->relatedUser = new UserProfileApi();
    }

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?bool $isPublished = true;
    public ?UserProfileApi $relatedUser = null;
    public ?DateTimeImmutable $createdAt = null;
    public ?\DateTimeInterface $startDate = null;
    public ?\DateTimeInterface $endDate = null;
    public ?bool $allDay = null;
    
    /**  @var array<int, SubscriptionApi> */
    public $subscribedTo;

    public ?SubscriptionApi $subscribedBy = null;

}