<?php

namespace App\ApiResource;

use App\Entity\StatusTransfer;
use DateTimeImmutable;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\ShopOrderApi;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\MolliePaymentStatusEnum;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\ORM\Mapping as ORM;


#[ApiResource(
    shortName: 'transaction',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationClientItemsPerPage: true,
    // paginationItemsPerPage: 10,
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: StatusTransfer::class),
    operations: [
        new Get(
            security: 'is_granted("ROLE_USER_STUDENT")',
        ),
        new GetCollection(
            security: 'is_granted("ROLE_USER_STUDENT")',
        ),
        new Post(
            security: 'is_granted("ROLE_USER_STUDENT")',
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_STUDENT")',
        ),
        // new Put(),
        new Delete(
            security: 'is_granted("ROLE_USER_SIFU")',
        ),
    ],
)]
class StatusTransferApi {

    public function __construct()
    {
        // $this->userOrder = new ShopOrderApi();
    }
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    
   /** @var ShopOrderApi $userOrder */ 
    public $userOrder;
     
    /** @var MolliePaymentStatusEnum $status */ 
    public $status = null;
     
    /** @var DateTimeImmutable $createdAt */ 
    public $createdAt = null;
    public ?string $transferId = null;
    public null|string $customer = null;
}