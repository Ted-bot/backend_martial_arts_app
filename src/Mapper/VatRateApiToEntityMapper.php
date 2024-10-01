<?php

namespace App\Mapper;

use App\Entity\VatRate;
use App\Entity\ProductVat;
use App\ApiResource\VatRateApi;
use App\Repository\VatRateRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: VatRateApi::class, to: VatRate::class)]
class VatRateApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private VatRateRepository $vatRateRepository,
        private MicroMapperInterface $microMapper,
    ){}
    
    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof VatRateApi);

        $entity = $dto->id ? $this->vatRateRepository->find($dto->id) : new VatRate();

        if(!$entity) {
            throw new \Exception('VatRate not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof VatRateApi);
        $entity = $to;
        assert($entity instanceof VatRate);

        $entity->setProcent($dto->procent);
        
        $entity->addRelatedProductVat($this->microMapper->map($dto->relatedProductVat,
            ProductVat::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ])
        ); // note:

        return $entity;
    }
}