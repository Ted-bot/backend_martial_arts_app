<?php

namespace App\Mapper;

use App\Entity\Group;
use App\Entity\UserProfile;
use App\ApiResource\GroupApi;
use App\Repository\GroupRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: GroupApi::class, to: Group::class)]
class GroupApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private GroupRepository $groupRepository,
        private MicroMapperInterface $microMapper
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof GroupApi);

        $entity = $dto->id ? $this->groupRepository->find($dto->id) : new Group();

        if(!$entity) {
            throw new \Exception('Group not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof GroupApi);
        $entity = $to;
        assert($entity instanceof Group);

        // $group = new Group();
        $entity->setName($dto->name);
        $entity->setCode($dto->code);
        
        $entity->addProfileGroup($this->microMapper->map($dto->profile, UserProfile::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ])); // note : add to group

        return $entity;
    }
}