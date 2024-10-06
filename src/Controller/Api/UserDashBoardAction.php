<?php

namespace App\Controller\Api;

use App\Class\Role;
use App\Entity\Product;
use App\Entity\PostEvent;
use App\ApiResource\UserApi;
use App\Entity\Subscription;
use App\ApiResource\ProductApi;
use App\Service\UserDashBoardHelper;
use App\Enum\MolliePaymentStatusEnum;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Dto\UserDashboard\UserDashBoardDtoDto;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class UserDashBoardAction extends AbstractController
{
    public function __construct(
        private EntityManager $entityManager,
        private UserDashBoardHelper $dashboardHelper
    ){}

    public function __invoke()
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $user = $this->getUser();
        
        if(empty($user->getSubscriptions()?->getValues()[0])){
            return 'No Valid Subscription Available'; // create DTO
        }

        /** @var Subscription $subscription Object */
        $subscription = $this->entityManager->getRepository(Subscription::class)->findOneBy(
            ['subscriptionOwnedBy' =>  $user->getId(), 'status' => MolliePaymentStatusEnum::PAID], 
            ['createdAt' => 'DESC']
        );
        
        if($subscription === null){
            return 'No Valid Subscription Available'; // create DTO
        }
        
        $userDashBoard = $this->dashboardHelper->userSubscriptionToApiDto(
            $subscription, 
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName()            
        );

        return $userDashBoard;        
    }
}


