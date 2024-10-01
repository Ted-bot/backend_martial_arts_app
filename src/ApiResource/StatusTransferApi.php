<?php

namespace App\ApiResource;

use App\Entity\StatusTransfer;
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
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\MolliePaymentStatusEnum;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotBlank;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Doctrine\ORM\Mapping as ORM;


#[ApiResource(
    shortName: 'transaction',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    paginationItemsPerPage: 10,
    // security: 'is_granted("ROLE_USER_STUDENT")',
    stateOptions: new Options(entityClass: StatusTransfer::class),
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
class StatusTransferApi {

    public function __construct()
    {
        // $this->userOrder = new ShopOrderApi();
    }
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
    public ?ShopOrderApi $userOrder;
    public ?MolliePaymentStatusEnum $status = null;
    public ?DateTimeImmutable $createdAt = null;
    public ?string $transferId = null;
    public null|string $customer = null;
}