<?php

namespace App\Mapper;

use App\Entity\ShopOrder;
use App\Entity\UserAddress;
use App\ApiResource\UserApi;
use App\ApiResource\AddressApi;
use App\ApiResource\ShopOrderApi;
use App\ApiResource\UserAddressApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: UserAddress::class, to: UserAddressApi::class)]
class UserAddressEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof UserAddress);

        $dto = new userAddressApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof UserAddress);
        assert($dto instanceof UserAddressApi);

        $dto->id = $entity->getId();
        
        $dto->addressUser = $this->microMapper->map($entity->getAddressUser(), UserApi::class,[
            MicroMapperInterface::MAX_DEPTH => 1
        ]);

        $dto->address = $this->microMapper->map($entity->getAddress(), AddressApi::class,[
            MicroMapperInterface::MAX_DEPTH => 1
        ]);

        $dto->isDefault = $entity->isDefault();
        
        $dto->shopOrders = array_map(function(ShopOrder $shopOrder) {
            return $this->microMapper->map($shopOrder, ShopOrderApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]); 
            },$entity->getShopOrders()->toArray()
        );

        return $dto;
    }
}