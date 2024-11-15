<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\ShopOrder;
use App\Entity\UserAddress;
use App\ApiResource\UserApi;
use App\ApiResource\ShopOrderApi;
use App\ApiResource\UserAddressApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ShopOrder::class, to: ShopOrderApi::class)]
class ShopOrderEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ){}
    
    public function load(object $from, string $toClass, array $context): object
    {
        
        $entity = $from;
        assert($entity instanceof ShopOrder);

        $dto = new ShopOrderApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof ShopOrder);
        assert($dto instanceof ShopOrderApi);
 
        $dto->orderDate = $entity->getOrderDate();
        // $dto->statusTransfer = $entity->getStatusTransfers(); // note: not really neede here only in StatusTransfer
        $dto->orderStatus = $entity->getOrderStatus();
        $dto->totalAmount = $entity->getTotalAmount();

        $dto->orderOwnedBy = $this->microMapper->map($entity->getOrderOwnedBy(), UserApi::class, [
            MicroMapperInterface::MAX_DEPTH => 1
        ]);

        if($entity->getShippingAddress()){
            $dto->shippingAddress = $this->microMapper->map($entity->getShippingAddress(), UserAddressApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]);
        }

        return $dto;
    }
}