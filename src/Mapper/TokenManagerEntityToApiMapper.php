<?php

namespace App\Mapper;

use App\Entity\Subscription;
use App\Entity\TokenManager;
use App\ApiResource\SubscriptionApi;
use App\ApiResource\TokenManagerApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\TokenManagerRepository;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMapper(from: TokenManager::class, to: TokenManagerApi::class)]
class TokenManagerEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private TokenManagerRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private MicroMapperInterface $microMapper
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof TokenManager);

        $dto = new TokenManagerApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof TokenManager);
        assert($dto instanceof TokenManagerApi);
 
        $dto->id = $entity->getId();
        $dto->uuid = $entity->getUuid();
        $dto->tokens = $entity->getTokens(); // note: create service to calculate
        
        $dto->relatedSubscription = $this->microMapper->map($entity->getRelatedSubscription(), SubscriptionApi::class, [
            MicroMapperInterface::MAX_DEPTH => 1
        ]);

        return $dto;
    }
}