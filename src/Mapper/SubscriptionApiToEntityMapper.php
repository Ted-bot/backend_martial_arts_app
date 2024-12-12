<?php

namespace App\Mapper;

use App\Entity\Product;
use App\Entity\Subscription;
use App\Entity\TokenManager;
use App\ApiResource\SubscriptionApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: SubscriptionApi::class, to: Subscription::class)]
class SubscriptionApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private Security $security,
        private MicroMapperInterface $microMapper,
        private SubscriptionRepository $subscriptionRepository,
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof SubscriptionApi);

        $entity = $dto->id ? $this->subscriptionRepository->find($dto->id) : new Subscription();

        if(!$$entity) {
            throw new \Exception('Subscription not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof SubscriptionApi);
        $entity = $to;
        assert($entity instanceof Subscription);

        $entity->setUuid($dto->uuid);
        $entity->setStatus($dto->status);
        $entity->setAmount($dto->amount);
        $entity->setTransferId($dto->transferId); // set transfer Id After paymendId is created
        $entity->setDuration($dto->duration); //SubscriptionLengthTypeEnum
        $entity->setDateStart($dto->dateStart); //SubscriptionLengthTypeEnum    
        $entity->setDateEnd($dto->dateEnd); //SubscriptionLengthTypeEnum    
        $entity->setCreatedAt($dto->createdAt); // check when updated the created at doesnt not update
        $entity->setUpdatedAt(); 
        
        $entity->setSubscribedProduct($this->microMapper->map($dto->subscribedProduct, Product::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]));
        
        if($dto->subscriptionOwnedBy){
            $entity->setSubscriptionOwnedBy($dto->subscriptionOwnedBy); // get user id            
        } else {
            $entity->setSubscriptionOwnedBy($this->security->getUser()); // get user id
        }
        
        $entity->setTokenManager($this->microMapper->map($dto->tokenManager, TokenManager::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]));

        return $entity; // return subscription or upload
    }
}