<?php

namespace App\ApiResource;

use App\Entity\Group;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\UserProfileApi;
use ApiPlatform\Metadata\ApiResource;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


#[ApiResource(
    shortName: 'Class',
    description: 'Group Entity',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationClientItemsPerPage: true,
    stateOptions: new Options(entityClass: Group::class),
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => ['class:read']
    ],
    denormalizationContext: [
        'groups' => ['class:write']
    ],
    
)]
class GroupApi 
{
    public function __construct()
    {
        $this->profileGroup = new ArrayCollection();
    }

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    public ?string $name = null;
    public ?int $code = null;

    /** @var array<int, UserProfileApi> */
    public $profileGroups;
    
    /** @var UserProfileApi $profile */
    public $profile = null;
    
}