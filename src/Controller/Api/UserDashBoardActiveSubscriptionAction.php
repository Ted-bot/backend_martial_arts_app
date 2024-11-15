<?php

namespace App\Controller\Api;

use App\Class\Role;
use App\Entity\User;
use App\Entity\Subscription;
use App\Service\UserDashBoardHelper;
use App\Enum\MolliePaymentStatusEnum;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class UserDashBoardActiveSubscriptionAction extends AbstractController
{
    public function __construct(
        private EntityManager $entityManager,
        private UserDashBoardHelper $dashboardHelper
    ){}

    public function __invoke()
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        /** @var User $user Object */
        $user = $this->getUser();
        // dd(['check out' => $user->getSubscriptions()?->getValues()[0]]);
        if(empty($user->getSubscriptions()?->getValues())){
            // return 'No Valid Subscription Available'; // create DTO
            return new Response('No Subscribed Events Available!', Response::HTTP_EXPECTATION_FAILED);
        }

        /** @var Subscription $subscription Object */
        $subscription = $this->entityManager->getRepository(Subscription::class)->findOneBy(
            ['subscriptionOwnedBy' =>  $user->getId(), 'status' => MolliePaymentStatusEnum::PAID], 
            ['createdAt' => 'DESC']
        );

        // dd(['check out' => $subscription]);
        
        if($subscription === null){
        return new Response('No Subscribed Events Available!', Response::HTTP_EXPECTATION_FAILED);
            // return 'No Valid Subscription Available'; // create DTO
        }

        // dd('isWorking', $user);
        
        $userDashBoard = $this->dashboardHelper->userSubscriptionToApiDto(
            $subscription, 
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName()            
        );

        // dd('isWorking', $userDashBoard);

        return $userDashBoard;        
        // return new JsonResponse($userDashBoard);        
    }
}


