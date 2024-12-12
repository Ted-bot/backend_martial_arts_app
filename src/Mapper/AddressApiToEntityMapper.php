<?php

namespace App\Mapper;

use App\Entity\Address;
use App\Entity\UserAddress;
use App\ApiResource\AddressApi;
use App\Repository\AddressRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: AddressApi::class, to: Address::class)]
class AddressApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private AddressRepository $userAddressRepository,
        private MicroMapperInterface $microMapper
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof AddressApi);

        $entity = $dto->id ? $this->userAddressRepository->find($dto->id) : new Address();

        if(!$entity) {
            throw new \Exception('Address not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof AddressApi);
        $entity = $to;
        assert($entity instanceof Address);

        // $entity->setAddressUser($this->microMapper->map($dto->addressUser, User::class, [
        //     MicroMapperInterface::MAX_DEPTH => 0
        // ]));

        // $entity->setAddress($this->microMapper->map($dto->address, Address::class, [
        //     MicroMapperInterface::MAX_DEPTH => 0
        // ]));

        // $entity->setDefault($dto->isDefault);
        // $entity->addShopOrder($context["addShopOrder"]); // 

        $entity->setUnitNumber($dto->unitNumber);
        $entity->setStreetNumber($dto->streetNumber);
        $entity->setAddressLine($dto->addressLine);
        $entity->setCity($dto->location); // set city
        $entity->setRegion($dto->region); // set state
        $entity->setPostalCode($dto->postalCode);
        
        $entity->addUserAddress($this->microMapper->map($dto->userAddress, UserAddress::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ])); // CountryTypeEnum::NL_CODEaddUserAddress

        return $entity; // return userAddress or upload
    }
}