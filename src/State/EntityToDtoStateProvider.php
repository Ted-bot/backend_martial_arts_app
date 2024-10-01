<?php

namespace App\State;

use ArrayIterator;
use App\Entity\User;
use App\ApiResource\UserApi;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class EntityToDtoStateProvider implements ProviderInterface
{
     public function __construct(
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectProvider,
        #[Autowire(service: ItemProvider::class)] private ProviderInterface $itemProvider,
        private EntityManager $entityManager,
        private Pagination $pagination,
        private MicroMapperInterface $microMapper
    ){

    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();

        if ($operation instanceof CollectionOperationInterface) {
            
            $entities = $this->collectProvider->provide($operation,$uriVariables, $context);
            
            $dtos = [];

            
            foreach ($entities as $entity){
                $dtos[] = $this->mapDtoToEntity($entity, $resourceClass);
            }

            // dd($dtos);
            
            return new TraversablePaginator(
                new ArrayIterator($dtos),
                $entities->getCurrentPage(),
                $entities->getItemsPerPage(),
                $entities->getTotalItems()
           );
        }
        
        $entity = $this->itemProvider->provide($operation, $uriVariables, $context);

        if(!$entity){
            return null;
        }

        return $this->mapDtoToEntity( $entity, $resourceClass);
    }

    private function mapDtoToEntity(object $entity, string $resourceClass): object
    {
        return $this->microMapper->map($entity, $resourceClass);
    }
}
