<?php

namespace App\Mapper;

use App\Entity\OrderLine;
use App\ApiResource\ProductApi;
use App\ApiResource\OrderLineApi;
use App\ApiResource\ShopOrderApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;


#[AsMapper(from: OrderLine::class, to: OrderLineApi::class)]
class OrderLineEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ){}

    
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof OrderLine);

        $dto = new OrderLineApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof OrderLine);
        assert($dto instanceof OrderLineApi);
 
        $dto->id = $entity->getId();
        $dto->price = $entity->getPrice();

        $dto->product = $this->microMapper->map($entity->getProduct(), ProductApi::class, [
            MicroMapperInterface::MAX_DEPTH => 1
        ]);

        $dto->shopOrder = $this->microMapper->map($entity->getShopOrder(), ShopOrderApi::class, [
            MicroMapperInterface::MAX_DEPTH => 1
        ]);
        $dto->qty = $entity->getQty();

        return $dto;
    }
}