<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\OrderLine;
use App\Entity\ProductVat;
use App\Entity\Subscription;
use App\ApiResource\ProductApi;
use App\Repository\ProductRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ProductApi::class, to: Product::class)]
class ProductApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private Security $security,
        private MicroMapperInterface $microMapper,
        private ProductRepository $productRepository
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof ProductApi);

        $entity = $dto->id ? $this->productRepository->find($dto->id) : new Product();

        if(!$entity) {
            throw new \Exception('Product not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof ProductApi);
        $entity = $to;
        assert($entity instanceof Product);

        // $entity = new Product();
        // $entityId = new ProductUUID(); // dt->uuid
        if($dto->name) $entity->setName($dto->name);
        if($dto->price) $entity->setPrice($dto->price);
        if($dto->category) $entity->setCategory($dto->category);
        if($dto->description) $entity->setDescription($dto->description);
        if($dto->images) $entity->setImages($dto->images);
        if($dto->isPublished) $entity->setPublished($dto->isPublished);
        if($dto->createdAt) $entity->setCreatedAt($dto->createdAt);
        if($dto->currency) $entity->setCurrencyType($dto->currency);
        if($dto->duration) $entity->setDuration($dto->duration);

        if($dto->relatedUser){  
            $entity->setRelatedUser($this->microMapper->map($dto->relatedUser, User::class, [
                MicroMapperInterface::MAX_DEPTH
            ]));
        } else {
            $entity->setRelatedUser($this->security->getUser());
        }  

        $entity->setSku($dto->sku);
        $entity->setDurationLength($dto->durationLength);
        $entity->setDirectOrPeriodic($dto->directOrPeriodic);
        
        $entity->addProductVat($this->microMapper->map($dto->productVat, ProductVat::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ])); // // note: ? add productVats

        $entity->addOrderLine($this->microMapper->map($dto->orderLine, OrderLine::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ])); // note: ? add Orderline

        $entity->addSubscription($this->microMapper->map($dto->subscription, Subscription::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ])); // note:? add subscription

        return $entity;
    }
}