<?php

namespace App\ApiResource;

use DateTimeInterface;
use App\Entity\ShopOrder;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\MolliePaymentStatusEnum;
use App\ApiResource\StatusTransferApi;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    shortName: 'shoporder',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationClientItemsPerPage: true,
    // paginationItemsPerPage: 10,
    // normalizationContext: ['groups' => ['read_customer']],
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: ShopOrder::class),
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
class ShopOrderApi {

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
        $this->statusTransfers = new ArrayCollection();

        // $this->orderOwnedBy = new UserApi();
        // $this->shippingAddress = new UserAddressApi();
    }


    // #[Groups(["read_customer"])] // "write_customer",
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    
    /** @var UserApi $orderOwnedBy */
    public $orderOwnedBy = null;

    public ?string $totalAmount = null;
    
    /** @var DateTimeInterface $orderDate */
    public $orderDate = null;
     
     /** @var UserAddressApi $shippingAddress */
    public $shippingAddress = null;
     
     /** @var MolliePaymentStatusEnum $orderStatus */
    public $orderStatus = null;
    
    /** @var array<int, OrderlineApi > */
    public $orderLines;
    
    /** @var array<int, StatusTransferApi > */
    private $statusTransfers;
    
}