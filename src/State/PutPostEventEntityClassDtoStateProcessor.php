<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\DeleteOperationInterface;
use App\Entity\PostEvent;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class PutPostEventEntityClassDtoStateProcessor implements ProcessorInterface
{
    public function __construct(
        // #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        // #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        #[Autowire(service: RemoveProcessor::class)]
        private ProcessorInterface $removeProcessor,
        private MicroMapperInterface $microMapper
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

       if('PUT' === $operation->getMethod()){
           // foreach($context['request']->attributes as $key => $property){
           //     if($key === 'data'){
           //         // dump([ 'data' => $property]);
           //         $property = $this->mapDtoToEntity($property, PostEvent::class);
           //     }
           // }
        $context['previous_data'] = $this->mapDtoToEntity($context['previous_data'], PostEvent::class);
        // dd(['hallo' => $operation->getMethod(), 'isPutMethod' => $operation->getMethod() === "PUT"]);
       }

        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);
        
        $data->id = $entity->getId();        
        
        return $data;
        // $this->sendWelcomeEmail($data);
    }

    private function mapDtoToEntity($dto, $entityClass): object
    {
        return $this->microMapper->map($dto, $entityClass);
    }
}
