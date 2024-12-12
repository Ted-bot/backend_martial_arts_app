<?php

namespace App\Mapper;

use App\Entity\PostEvent;
use App\Entity\UserProfile;
use App\ApiResource\PostEventApi;
use App\ApiResource\UserProfileApi;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: PostEvent::class, to: PostEventApi::class)]
class PostEventEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ){}
    
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof PostEvent);

        $dto = new PostEventApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof PostEvent);
        assert($dto instanceof PostEventApi);
 
        $dto->title = $entity->getTitle();
        $dto->description = $entity->getDescription();

        $dto->relatedUser = $this->microMapper->map($entity->getRelatedUser(), UserProfileApi::class, [
            MicroMapperInterface::MAX_DEPTH => 1
        ]);

        $dto->isPublished = $entity->getPublished();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->startDate = $entity->getStartDate();
        $dto->endDate = $entity->getEndDate();
        $dto->allDay = $entity->isAllDay();
        
        $dto->subscribedTo = array_map(function(UserProfile $product) {
            return $this->microMapper->map($product, UserProfileApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]); 
            },$entity->getSubscribe()->toArray()
        );

        return $dto;
    }
}