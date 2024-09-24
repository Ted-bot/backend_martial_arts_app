<?php

namespace App\Controller\Api;

use App\Class\Role;
use App\Class\Roles;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

#[AsController]
class ClientDashboardController extends AbstractController
{
    public $subRepo;
    public $jwtEncoder;
    
    public function __construct(
            SubscriptionRepository $subRepo,
            JWTEncoderInterface $jwtEncoder
        )
        {
            $this->subRepo = $subRepo;
        }
        
        #[Route(
            '/api/v1/dashboard/user',
            name: 'app_client_dashboard',
            methods: 'GET'
        )]
        public function dashboardData(#[CurrentUser] ?User $user, Request $request, JWTTokenManagerInterface $jwtTokenManager): JsonResponse
    {
        $user = $this->getUser();
        // $subscription = $this->subRepo->findByIdThenReturnArray($this->getUser()->getId());
        $subscription = $this->subRepo->findOneBy(['subscriptionOwnedBy' => $user->getId()],['createdAt' => 'DESC']);
        $userData = array('id' => $user->getId(),'first_name'=> $user->getFirstName(), 'email' => $user->getEmail(), 'last_name' => $user->getLastName());
        $subscription !== null && $userData['subscription'] = $subscription;
        return $this->json($userData, 200);
    }

}   
