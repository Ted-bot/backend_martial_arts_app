<?php

namespace App\Mapper;

use App\Entity\Product;
use App\Entity\ProductVat;
use App\ApiResource\ProductVatApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\ProductVatRepository;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ProductVatApi::class, to: ProductVat::class)]
class ProductVatApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private ProductVatRepository $productVatRepository,
        private MicroMapperInterface $microMapper
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof ProductVatApi);

        $entity = $dto->id ? $this->productVatRepository->find($dto->id) : new ProductVat();

        if(!$entity) {
            throw new \Exception('ProductVat not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof ProductVatApi);
        $entity = $to;
        assert($entity instanceof ProductVat);

        $entity->setVatRate($dto->vatRate);
        $entity->setVatAmount($dto->vatAmount);
        $entity->setProduct($this->microMapper->map($dto->product, Product::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]));

        return $entity;
    }
}