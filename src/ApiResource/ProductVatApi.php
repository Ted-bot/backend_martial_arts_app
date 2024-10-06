<?php

namespace App\ApiResource;

use App\Entity\User;
use App\Entity\Address;
use App\Entity\ProductVat;
use App\Entity\UserProfile;
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
    stateOptions: new Options(entityClass: ProductVat::class),
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
class ProductVatApi 
{
    public function __construct()
    {
        $this->profileGroup = new ArrayCollection();
        // $this->vatRate = new VatRateApi();
    }

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    
    /** @var VatRateApi $varRate */
    public $vatRate = null;
    public ?string $vatAmount = null;
    
    /** @var ProductApi $product */
    public $product = null;
    
}