<?php

namespace App\Mapper;

use App\Entity\OrderLine;
use App\Entity\ShopOrder;
use App\ApiResource\OrderLineApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\OrderLineRepository;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: OrderLineApi::class, to: OrderLine::class)]
class OrderLineApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private OrderLineRepository $orderLineRepository,
        private MicroMapperInterface $microMapper
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof OrderLineApi);

        $orderLineEntity = $dto->id ? $this->orderLineRepository->find($dto->id) : new OrderLine();

        if(!$orderLineEntity) {
            throw new \Exception('OrderLine not found!');
        }

        return $orderLineEntity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof OrderLineApi);
        $entity = $to;
        assert($entity instanceof OrderLine);

        // $orderLine = new OrderLine();
        // $orderLineId = new OrderLineUUID(); // dt->uuid

        $entity->setPrice($dto->price);
        $entity->setProduct($dto->product);
        $entity->setShopOrder($this->microMapper->map($dto->shopOrder, ShopOrder::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]));
        $entity->setQty($dto->qty);

        return $entity; // return orderLine or upload
    }
}