<?php

namespace App\Mapper;

use App\Entity\PostEvent;
use App\Entity\UserProfile;
use App\ApiResource\PostEventApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\PostEventRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: PostEventApi::class, to: PostEvent::class)]
class PostEventApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private PostEventRepository $postEventRepository,
        private MicroMapperInterface $microMapper,
        private Security $security
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof PostEventApi);

        $entity = $dto->id ? $this->postEventRepository->find($dto->id) : new PostEvent();

        if(!$entity) {
            throw new \Exception('PostEvent not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof PostEventApi);
        $entity = $to;
        assert($entity instanceof PostEvent);

        $entity->setTitle($dto->title);
        $entity->setDescription($dto->description);
        
        if($dto->relatedUser){  
            $entity->setRelatedUser($this->microMapper->map($dto->relatedUser, UserProfile::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]));
        } else {
            $entity->setRelatedUser($this->security->getUser());
        } 

        $entity->setPublished($dto->isPublished);
        $entity->setCreatedAt($dto->createdAt);
        $entity->setStartDate($dto->startDate);
        $entity->setEndDate($dto->endDate);
        $entity->setAllDay($dto->allDay);

        if($dto->subscribedBy){
            $entity->addSubscribe($this->microMapper->map($dto->subscribedBy, UserProfile::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]));
        }

        return $entity; // return orderLine or upload
    }
}