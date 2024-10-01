<?php

namespace App\Mapper;

use App\Entity\VatRate;
use App\Entity\ProductVat;
use App\ApiResource\VatRateApi;
use App\ApiResource\ProductVatApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: VatRate::class, to: VatRateApi::class)]
class VatRateEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ){}
    
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof VatRate);

        $dto = new VatRateApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof VatRate);
        assert($dto instanceof VatRateApi);
 
        $dto->id = $entity->getId();

        $dto->procent = $entity->getProcent();
        
        $dto->relatedProductVat = array_map(function(ProductVat $shopOrder) {
            return $this->microMapper->map($shopOrder, ProductVatApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]); 
            }, $entity->getRelatedProductVat()->toArray()
        );

        return $dto;
    }
}