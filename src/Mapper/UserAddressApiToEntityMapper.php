<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\Address;
use App\Entity\UserAddress;
use App\ApiResource\UserAddressApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\UserAddressRepository;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: UserAddressApi::class, to: UserAddress::class)]
class UserAddressApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private UserAddressRepository $userAddressRepository,
        private MicroMapperInterface $microMapper
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof UserAddressApi);

        $entity = $dto->id ? $this->userAddressRepository->find($dto->id) : new UserAddress();

        if(!$entity) {
            throw new \Exception('UserAddress not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof UserAddressApi);
        $entity = $to;
        assert($entity instanceof UserAddress);

        if($dto->addressUser){
            $entity->setAddressUser($this->microMapper->map($dto->addressUser, User::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]));
        }

        if($dto->address){
            $entity->setAddress($this->microMapper->map($dto->address, Address::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]));
        }

        if($dto->isDefault) $entity->setDefault($dto->isDefault);
        // $entity->addShopOrder($context["addShopOrder"]); // 

        return $entity; // return userAddress or upload
    }
}