<?php

namespace App\State;

use App\Entity\User;
use App\Entity\UserProfile;
use App\ApiResource\UserApi;
use ApiPlatform\Metadata\Operation;
use App\ApiResource\UserProfileApi;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\DeleteOperationInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EntityClassDtoStateProcessor implements ProcessorInterface
{
    public function __construct(
        // #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        // #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        #[Autowire(service: RemoveProcessor::class)]
        private ProcessorInterface $removeProcessor,
        private MicroMapperInterface $microMapper,
        private EntityManager $entityManager
    ){

    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $stateOptions = $operation->getStateOptions();
        assert($stateOptions instanceof Options);
        $entityClass = $stateOptions->getEntityClass();
        
        $entity = $this->mapDtoToEntity($data, $entityClass);
        
        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);
            return null;
        }

        if($operation->getMethod() === "PUT"){
            foreach($context["request"]->attributes as $key => $property){
                if($key === 'data'){
                    $context["previous_data"] = $this->mapDtoToEntity($property, $entityClass);
                    // $property = $this->mapDtoToEntity($property, User::class);
                }
            }
            // $context["previous_data"] = $this->mapDtoToEntity($context["previous_data"], User::class);
        }

        // dd(['created entity' => $entity]);
        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);
        
        if($operation->getMethod() === "POST" && assert($entity instanceof User)){
            // dd("it passes");
            // $getLatestUser = $this->entityManager->getRepository(User::class)->findOneBy([], ['id' => 'desc']);
            $newProfile = new UserProfileApi();
            // $sequenceUser = $getLatestUser->getId();
            // $sequenceNr = $getLatestUser->getId() + 1;
            $newProfile->userUniq =  $this->mapDtoToEntity($entity, UserApi::class);
            // $newProfile->userUniq =  $this->mapDtoToEntity($entity, UserApi::class);/
            // $newProfile->userUniq = $sequenceNr;
            
            $userProfile = $this->microMapper->map($newProfile, UserProfile::class);
            
            // $userProfile->setUserUniq($entity);
            $this->entityManager->persist($userProfile);
            $this->entityManager->flush();
        }

        $data->id = $entity->getId();
        
        return $data;
        // $this->sendWelcomeEmail($data);
    }

    private function mapDtoToEntity($dto, $entityClass): object
    {
        return $this->microMapper->map($dto, $entityClass);
    }
}
