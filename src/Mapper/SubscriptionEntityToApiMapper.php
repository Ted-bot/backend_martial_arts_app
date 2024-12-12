<?php

namespace App\Mapper;

use App\ApiResource\UserApi;
use App\Entity\Subscription;
use App\ApiResource\ProductApi;
use App\ApiResource\SubscriptionApi;
use App\ApiResource\TokenManagerApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Subscription::class, to: SubscriptionApi::class)]
class SubscriptionEntityToApiMapper implements MapperInterface
{

    public function __construct(
        private MicroMapperInterface $microMapper
    )
    {}
    
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Subscription);

        $dto = new SubscriptionApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Subscription);
        assert($dto instanceof SubscriptionApi);

        $dto->uuid = $entity->getUuid();
        $dto->status = $entity->getStatus();
        $dto->amount = $entity->getAmount();
        $dto->transferId = $entity->getTransferId(); // set transfer Id After paymendId is created
        $dto->duration = $entity->getDuration(); //SubscriptionLengthTypeEnum
        $dto->dateStart = $entity->getDateStart(); //SubscriptionLengthTypeEnum    
        $dto->dateEnd = $entity->getDateEnd(); //SubscriptionLengthTypeEnum    
        $dto->createdAt = $entity->getCreatedAt(); // check when updated the created at doesnt not update
        $dto->updatedAt = $entity->getUpdatedAt(); 
        
        if($entity->getSubscribedProduct()){
            $dto->subscribedProduct = $this->microMapper->map($entity->getSubscribedProduct(), ProductApi::class, [
            MicroMapperInterface::MAX_DEPTH => 1
            ]);
        }   

        if($entity->getSubscriptionOwnedBy()){
            $dto->subscriptionOwnedBy = $this->microMapper->map($entity->getSubscriptionOwnedBy(), UserApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]); // get user id
        }

        if($entity->getTokenManager()){
            $dto->tokenManager = $this->microMapper->map($entity->getTokenManager(), TokenManagerApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]);
        }

        return $dto;
    }
}