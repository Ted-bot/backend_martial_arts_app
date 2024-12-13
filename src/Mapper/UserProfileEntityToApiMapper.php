<?php

namespace App\Mapper;

use App\ApiResource\SubscriptionApi;
use App\Entity\PostEvent;
use App\Entity\UserProfile;
use App\ApiResource\UserApi;
use App\ApiResource\GroupApi;
use App\ApiResource\PostEventApi;
use App\ApiResource\UserProfileApi;
use App\Enum\MolliePaymentStatusEnum;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\SubscriptionRepository;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: UserProfile::class, to: UserProfileApi::class)]
class UserProfileEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SubscriptionRepository $subscriptionRepository,
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof UserProfile);

        $dto = new UserProfileApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof UserProfile);
        assert($dto instanceof UserProfileApi);

        $dto->id = $entity->getId();
        $dto->description = $entity->getDescription();
        $dto->username = $entity->getUserName();

        if($entity->getUserUniq()){
            $dto->userUniq = $this->microMapper->map($entity->getUserUniq(), UserApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]);
        }
        
        // $dto->groupStudent = $this->microMapper->map($entity->getGroupStudent(), GroupApi::class, [
        //     MicroMapperInterface::MAX_DEPTH => 0
        // ] ); // note: when users are set in groups uncomment else it will give error :set on null

        
        $subscription = $this->subscriptionRepository->findOneBy(
                ['subscriptionOwnedBy' =>  $entity->getId(), 'status' => MolliePaymentStatusEnum::PAID], 
                ['createdAt' => 'DESC']
            );     

        if($subscription){
            $dto->tokens = $subscription->getTokenManager()->getTokens();
        }

        if($entity->getGroupStudent()){
            $dto->groupStudent = $entity->getGroupStudent();
        }

        if($entity->getSubscribeToEvents()?->toArray()){
            $dto->subscribeToEvents = array_map(function(PostEvent $postEvent) {
                return $this->microMapper->map($postEvent, PostEventApi::class, [
                    MicroMapperInterface::MAX_DEPTH => 1
                ]); 
                }, $entity->getSubscribeToEvents()->toArray()
            );
        }

        return $dto;
    }
}