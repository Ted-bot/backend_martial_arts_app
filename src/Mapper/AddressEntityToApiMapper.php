<?php

namespace App\Mapper;

use App\Entity\Address;
use App\Entity\UserAddress;
use App\ApiResource\AddressApi;
use App\ApiResource\UserAddressApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Address::class, to: AddressApi::class)]
class AddressEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Address);

        $dto = new AddressApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Address);
        assert($dto instanceof AddressApi);

        $dto->unitNumber = $entity->getUnitNumber();
        $dto->streetNumber = $entity->getStreetNumber();
        $dto->addressLine = $entity->getAddressLine();
        $dto->location = $entity->getCity(); // set city
        $dto->region = $entity->getRegion(); // set state
        $dto->postalCode = $entity->getPostalCode();
        
        $dto->userAddresses = array_map(function(UserAddress $product) {
            return $this->microMapper->map($product, UserAddressApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]); 
            },$entity->getUserAddresses()->toArray()
        );

        return $dto;
    }
}