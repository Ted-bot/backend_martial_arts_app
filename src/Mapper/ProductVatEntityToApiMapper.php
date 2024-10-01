<?php

namespace App\Mapper;

use App\Entity\Product;
use App\Entity\VatRate;
use App\Entity\ProductVat;
use App\ApiResource\ProductApi;
use App\ApiResource\VatRateApi;
use App\ApiResource\ProductVatApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\ProductVatRepository;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ProductVat::class, to: ProductVatApi::class)]
class ProductVatEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof ProductVat);

        $dto = new ProductVatApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof ProductVat);
        assert($dto instanceof ProductVatApi);
 
        $dto->id = $entity->getId();
        
        $dto->vatRate = $this->microMapper->map($entity->getVatRate(), VatRateApi::class, [
            MicroMapperInterface::MAX_DEPTH => 1

        ]);
        $dto->vatAmount = $entity->getVatAmount();
        $dto->product = $this->microMapper->map($entity->getProduct(), ProductApi::class, [

            MicroMapperInterface::MAX_DEPTH => 1
        ]);

        return $dto;
    }
}