<?php

namespace App\ApiPlatform;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserSecurityExtention implements QueryCollectionExtensionInterface 
{
    
public function __construct(
        // private AccessDecisionManagerInterface $accessDecisionManager,
        private Security $security
    )
    {}
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $user = $this->security->getUser();
        // dd(['it\'s admin user ' => $user, 'access' => $this->security->isGranted('ROLE_SIFU')]);

        if(User::class !== $resourceClass){
            return;

        }

        
        if(!$this->security->isGranted('ROLE_SIFU')){
            return;
        } 

        
        if($user && $this->security->isGranted('ROLE_SIFU')){
            // $rootAlias = 
            $queryBuilder->getRootAliases()[0];
        }
        // $queryBuilder->andWhere(sprintf('%s.'));
    }
}