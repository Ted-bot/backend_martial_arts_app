<?php

namespace App\ApiResource;

use App\Entity\OrderLine;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Product;
use App\Entity\ShopOrder;
use App\Entity\UserAddress;
use App\Entity\UserProfile;
use App\Entity\Subscription;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\ShopOrderApi;
use App\ApiResource\UserAddressApi;
use App\ApiResource\SubscriptionApi;
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
use App\ApiResource\ProductApi;
use Doctrine\ORM\Mapping as ORM;


#[ApiResource(
    // shortName: 'orderline',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationItemsPerPage: 5,
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: OrderLine::class),
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
class OrderLineApi 
{
    public function __construct()
    {
        // $this->product = new ProductApi();
        // $this->shopOrder = new ShopOrderApi();
    }

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id;
    public ?ShopOrderApi $shopOrder;
    public ?ProductApi $product;
    public ?string $price = null;
    public ?int $qty = null;
}