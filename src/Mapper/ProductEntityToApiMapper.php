<?php

namespace App\Mapper;

use App\Entity\Product;
use App\Entity\OrderLine;
use App\Entity\ProductVat;
use App\ApiResource\UserApi;
use App\Entity\Subscription;
use App\ApiResource\ProductApi;
use App\ApiResource\OrderLineApi;
use App\ApiResource\ProductVatApi;
use App\ApiResource\SubscriptionApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Product::class, to: ProductApi::class)]
class ProductEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Product);

        $dto = new ProductApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Product);
        assert($dto instanceof ProductApi);
 
        $dto->name = $entity->getName();
        $dto->sku = $entity->getSku();
        $dto->price = $entity->getPrice();
        $dto->category = $entity->getCategory();
        $dto->description = $entity->getDescription();
        $dto->images = $entity->getImages();
        $dto->isPublished = $entity->isPublished();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->currencyType = $entity->getCurrencyType();
        $dto->duration = $entity->getDuration();
        $dto->relatedUser = $this->microMapper->map($entity->getRelatedUser(), UserApi::class, [
            MicroMapperInterface::MAX_DEPTH => 1
        ]);

        $dto->productVats = array_map(function(ProductVat $productVat) {
            return $this->microMapper->map($productVat, ProductVatApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]); 
            }, $entity->getProductVats()->toArray()
        );

        $dto->orderLines =  array_map(function(OrderLine $productVat) {
                return $this->microMapper->map($productVat, OrderLineApi::class, 
                [MicroMapperInterface::MAX_DEPTH => 1]
                ); 
                }, $entity->getOrderLines()->toArray()
            );

        $dto->durationLength = $entity->getDurationLength();
        $dto->directOrPeriodic = $entity->getDirectOrPeriodic();

        // $dto->subscription = $entity->getSubscriptions()->toArray();
        $dto->subscriptions = array_map(function(Subscription $subscription) {
            return $this->microMapper->map($subscription, SubscriptionApi::class, 
            [MicroMapperInterface::MAX_DEPTH => 1]
            ); 
            }, $entity->getSubscriptions()->toArray()
        );

        return $dto;
    }
}