<?php

namespace App\Mapper;

use App\Entity\Group;
use App\Entity\UserProfile;
use App\ApiResource\GroupApi;
use App\ApiResource\UserProfileApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Group::class, to: GroupApi::class)]
class GroupEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Group);

        $dto = new GroupApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Group);
        assert($dto instanceof GroupApi);
 
        $dto->id = $entity->getId();
        $dto->name = $entity->getName();
        $dto->code = $entity->getCode();

        $dto->profileGroup = array_map(function(UserProfile $product) {
            return $this->microMapper->map($product, UserProfileApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]); 
            },$entity->getProfileGroup()->toArray()
        );

        return $dto;
    }
}