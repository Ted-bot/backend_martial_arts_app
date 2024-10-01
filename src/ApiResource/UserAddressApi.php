<?php

namespace App\ApiResource;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\UserAddress;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\ShopOrderApi;
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
    // shortName: 'useraddress',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationItemsPerPage: 10,
    // normalizationContext: ['groups' =>  ['read_customer']],
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: UserAddress::class),
    operations: [
        new Get(
            provider: EntityToDtoStateProvider::class,
            processor: EntityClassDtoStateProcessor::class,
        ),
        new GetCollection(),
        // new Post(
        //     security: 'is_granted("PUBLIC_ACCESS")',
        //     validationContext:['groups' => ['Default', 'postValidation']]
        // ),
        new Patch(),
        // new Put(),
        new Delete(),
    ],
)]
class UserAddressApi 
{
    public function __construct()
    {
        $this->shopOrders = new ArrayCollection();
        // $this->addressUser = new UserApi();
        // $this->address = new AddressApi();
    }

    // #[Groups(["read_customer"])] //, "write_customer"
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    public ?int $id = null;
    
    /** @var UserApi Object */
    public $addressUser;
    
    /** @var AddressApi Obejct */
    public $address;

    public ?bool $isDefault = null;
    
    /**  @var array<int, ShopOrderApi >  */
    public $shopOrders;
}