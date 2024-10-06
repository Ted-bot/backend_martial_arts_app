<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UserDashboardStateProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        #[Autowire(service: ItemProvider::class)] Private ProviderInterface $providerInterface
        )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $userDashboardData = $this->providerInterface->provide($operation, $uriVariables, $context);
        
        // $user = $this->security->getUser();
        dd(['user' => $userDashboardData]);
        // Retrieve the state from somewhere
    }
}
