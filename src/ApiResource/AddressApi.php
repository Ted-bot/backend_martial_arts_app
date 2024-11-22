<?php

namespace App\ApiResource;

use App\Entity\User;
use App\Entity\Address;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\Enum\CountryTypeEnum;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\UserAddressApi;
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
    stateOptions: new Options(entityClass: Address::class),
    operations: [
        new Get(
            security: 'is_granted("ROLE_USER_STUDENT")', 
        ),
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_USER_STUDENT")', 
            // validationContext:['groups' => ['Default', 'postValidation']]
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_STUDENT")', 
        ),
        // new Put(),
        new Delete(),
    ],
)]
class AddressApi 
{
    public function __construct()
    {
        // $this->userAddresses = new ArrayCollection();
    }

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    public ?string $unitNumber = null;
    public ?int $streetNumber = null;
    public ?string $addressLine = null;
    public ?string $location = null;
    public ?string $region = null;
    public ?string $postalCode = null;
    
    /** @var CountryTypeEnum $country */
    public $country = null;
    
    /** @var array<int, UserAddressApi> */
    public $userAddresses;
    
    public ?UserAddressApi $userAddress = null;

}