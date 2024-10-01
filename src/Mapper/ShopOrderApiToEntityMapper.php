<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\ShopOrder;
use App\ApiResource\ShopOrderApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\ShopOrderRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ShopOrderApi::class, to: ShopOrder::class)]
class ShopOrderApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private Security $security,
        private MicroMapperInterface $microMapper,
        private ShopOrderRepository $shopOrderRepository
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof ShopOrderApi);

        $entity = $dto->id ? $this->shopOrderRepository->find($dto->id) : new ShopOrder();

        if(!$entity) {
            throw new \Exception('ShopOrder not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof ShopOrderApi);
        $entity = $to;
        assert($entity instanceof ShopOrder);

        $entity->setOrderDate($dto->orderDate);
        $entity->setOrderStatus($dto->orderStatus);
        $entity->setTotalAmount($dto->totalAmount);

        if($dto->orderOwnedBy){
            $entity->setOrderOwnedBy($this->microMapper->map($dto->orderOwnedBy, User::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]));        
        } else {
            $entity->setOrderOwnedBy($this->security->getUser());
        }        

        $entity->setShippingAddress($dto->shippingAddress);

        return $entity;
    }
}