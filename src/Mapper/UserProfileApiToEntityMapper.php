<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\Group;
use App\Entity\PostEvent;
use App\Entity\UserProfile;
use App\Entity\TokenManager;
use App\Repository\UserProfileRepository;
use App\ApiResource\UserProfileApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

#[AsMapper(from: UserProfileApi::class, to: UserProfile::class)]
class UserProfileApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private UserProfileRepository $userProfileRepository,
        private MicroMapperInterface $microMapper,
        private Security $security,
        private PropertyAccessorInterface $propertyAccessor
    ){}
    

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof UserProfileApi);

        $entity = $dto->id ? $this->userProfileRepository->find($dto->id) : new UserProfile();
        
        if(!$entity) {
            throw new \Exception('UserProfile not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof UserProfileApi);
        $entity = $to;
        assert($entity instanceof UserProfile);

        $entity->setDescription($dto->description);
        $entity->setUserName($dto->username);

        if($dto->userUniq){
            $entity->setUserUniq($this->microMapper->map($dto->userUniq, User::class, 
            [MicroMapperInterface::MAX_DEPTH => 0]
        ));
        } else {
            $entity->setUserUniq($this->security->getUser());
        }

        $entity->setWebsiteUrl($dto->websiteUrl);

        if($dto->groupStudent){
            $entity->setgroupStudent($this->microMapper->map($dto->groupStudent, Group::class,
                [MicroMapperInterface::MAX_DEPTH => 0]
            ));
        }

        if(!empty($dto->subscribeToEvents)) {
            
            $subscribeToEvents = [];
            foreach($dto->subscribeToEvents as $event){
                $subscribeToEvents[] = $this->microMapper->map($event, PostEvent::class, [
                    MicroMapperInterface::MAX_DEPTH => 1
                ]); // note: 
            }
            // dd(['subscribeToEvents' => $subscribeToEvents]);
            $this->propertyAccessor->setValue($entity, 'subscribeToEvents', $subscribeToEvents);
        }

        // note: $dto->postEvents conflicts with $dto->subscribeToEvents because both related to same resource PostEvents
        // solution make IRI of every postEvent, tricky is making sure if oke to casade delete object -> orpanRemoval
        // disable code to : (1 - prevent error because relatedUser in not allowd to bu null and prevent), (2 - set empty all subscribed (post)events user subscribed) 
        // if(!empty($dto->postEvents)) {
        //     $postEvents = [];
        //     foreach($dto->postEvents as $event){
        //         $postEvents[] = $this->microMapper->map($event, PostEvent::class, [
        //             MicroMapperInterface::MAX_DEPTH => 0
        //         ]); // note: 
        //     }
        //     $this->propertyAccessor->setValue($entity, 'postEvents', $postEvents);
        // }

        // note: by commenting this out will prevent tokenManagers to cause error because it tries to give empty [] need to fix this somehow
        // solution make IRI of every TokenManager, tricky is making sure if oke to casade delete object -> orpanRemoval
        // if(!empty($dto->tokenManagers)){
        //     $tokenManagers = [];
        //     foreach($dto->tokenManagers as $tokenManager){
        //         $tokenManagers[] = $this->microMapper->map($tokenManager, TokenManager::class, [
        //             MicroMapperInterface::MAX_DEPTH => 0
        //         ]);

        //         // $entity->addTokenManager($this->microMapper->map($tokenManager, TokenManager::class, [
        //         //     MicroMapperInterface::MAX_DEPTH => 0
        //         // ])); // note: delete not needed 
        //     }
        //     $this->propertyAccessor->setValue($entity, 'tokenManagers', $tokenManagers);
        // }

        return $entity;
    }
}