<?php

namespace App\Controller\Api;

use App\Class\Role;
use App\Class\Roles;
use App\Entity\User;
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
    public $userRepository;
    public $jwtEncoder;
    
    public function __construct(
            UserRepository $userRepository,
            JWTEncoderInterface $jwtEncoder
        )
        {
            $this->userRepository = $userRepository;
            $this->jwtEncoder = $jwtEncoder;
        }
        
        // public function __invoke(): Response
        // {
        //     // $this->denyAccessUnlessGranted(Roles::ROLE_USER_STUDENT);
            
        //     // return new Response($this->getUser());
        //     return new Response($this->getUser());
        // }
        
        #[Route(
            '/api/v1/dashboard/user',
            name: 'app_client_dashboard',
            methods: 'GET'
        )]
        public function dashboardData(#[CurrentUser] ?User $user, Request $request, JWTTokenManagerInterface $jwtTokenManager): JsonResponse
    {
        $user = $this->getUser();

        return $this->json(array('id' => $user->getId(),'first_name'=> $user->getFirstName(), 'email' => $user->getEmail(), 'last_name' => $user->getLastName()), 200);
    }

    /**
     * Get the value of userRepository
     */ 
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     * Set the value of userRepository
     *
     * @return  self
     */ 
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;

        return $this;
    }
}   
