<?php

namespace App\ApiResource;

use DateTimeImmutable;
use App\Entity\Product;
use App\ApiResource\UserApi;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\CategoryTypeEnum;
use App\Enum\CurrencyTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use App\ApiResource\OrderLineApi;
use App\Enum\SubscriptionTypeEnum;
use App\ApiResource\SubscriptionApi;
use ApiPlatform\Metadata\ApiResource;
use App\State\EntityToDtoStateProvider;
use App\Enum\SubscriptionLengthTypeEnum;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use App\Enum\SubscriptionDirectOrPeriodicTypeEnum;
use Symfony\Component\Serializer\Attribute\Groups;


#[ApiResource(
    shortName: 'product',
    description: 'Available Products',
    // filters: ['app.product.search_filter'],
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationClientItemsPerPage: true,
    // paginationItemsPerPage: 5,
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: Product::class),
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
    //     // new Put(),
        new Delete(),
    ],
    // normalizationContext: [
    //     'groups' => ['product:read']
    // ],
    // denormalizationContext: [
    //     'groups' => ['product:write']
    // ],
)]
class ProductApi 
{
    public function  __construct()
    {
        $this->relatedUser = new UserApi();
    }
    
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    public ?string $description = null;

    public ?array $images = null;

    public ?string $sku = null;   

    #[Groups(['product:read'])]
    public ?string $name = null;

    public ?string $price = null;

    public ?CategoryTypeEnum $category = null;

    public ?bool $isPublished = true;

    public ?CurrencyTypeEnum $currency = null;

    /**  @var array<int, ProductVatApi>  */
    public $productVats;
    
    public ?ProductVatApi $productVat;

    /** @var DateTimeImmutable */
    public $createdAt = null;

    /** @var SubscriptionTypeEnum */
    public $duration = null;

    /** @var UserApi Object */
    public $relatedUser;

    /** @var array<int, OrderLineApi> */
    public $orderLines;
    
   /** @var OrderLineApi */ 
    public $orderLine;
    /** @var SubscriptionApi */ 
    public $subscription;

    /** @var SubscriptionLengthTypeEnum */ 
    public $durationLength = null;

    /** @var SubscriptionDirectOrPeriodicTypeEnum */ 
    public $directOrPeriodic = null;

    /** @var array<int, SubscriptionApi> */
    public $subscriptions;
}