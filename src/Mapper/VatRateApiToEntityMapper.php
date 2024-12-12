<?php

namespace App\Mapper;

use App\Entity\VatRate;
use App\Entity\ProductVat;
use App\ApiResource\VatRateApi;
use App\Repository\VatRateRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

#[AsMapper(from: VatRateApi::class, to: VatRate::class)]
class VatRateApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private VatRateRepository $vatRateRepository,
        private MicroMapperInterface $microMapper,
        private PropertyAccessorInterface $propertyAccessor
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
        
        if(!empty($dto->relatedProductVat)) {
            
            $productVatArray = [];
            foreach($dto->relatedProductVat as $event){
                $productVatArray[] = $this->microMapper->map($event, ProductVat::class, [
                    MicroMapperInterface::MAX_DEPTH => 0
                ]); // note: 
            }
            // dd(['productVatArray' => $productVatArray]);
            $this->propertyAccessor->setValue($entity, 'relatedProductVat', $productVatArray);
        }
        // $entity->addRelatedProductVat($this->microMapper->map($dto->relatedProductVat,
        //     ProductVat::class, [
        //         MicroMapperInterface::MAX_DEPTH => 0
        //     ])
        // ); // note:

        return $entity;
    }
}