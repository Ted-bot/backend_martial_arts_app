<?php

namespace App\Mapper;

use App\Entity\Subscription;
use App\Entity\TokenManager;
use App\ApiResource\TokenManagerApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\TokenManagerRepository;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: TokenManagerApi::class, to: TokenManager::class)]
class TokenManagerApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private TokenManagerRepository $tokenManagerRepository,
        private MicroMapperInterface $microMapper
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof TokenManagerApi);

        $entity = $dto->id ? $this->tokenManagerRepository->find($dto->id) : new TokenManager();

        if(!$entity) {
            throw new \Exception('TokenManager not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof TokenManagerApi);
        $entity = $to;
        assert($entity instanceof TokenManager);

        $entity->setUuid($dto->uuid);
        $entity->setTokens($dto->tokens); // note: create service to calculate
        $entity->setRelatedSubscription($this->microMapper->map($dto->relatedSubscription, Subscription::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]));

        return $entity;
    }
}