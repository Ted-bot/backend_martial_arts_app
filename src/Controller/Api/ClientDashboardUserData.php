<?php

namespace App\Controller\Api;

use App\Class\Roles;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

#[AsController]
class ClientDashboardUserData extends AbstractController
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
        public function dashboardData(Request $request, JWTTokenManagerInterface $jwtTokenManager): Response
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'X-Authorization'
        );

        $token = $extractor->extract($request);
        $decodeToken = $this->jwtEncoder->decode($token);
        $user = $this->userRepository->findOneBy(array('email'=> $decodeToken['username']));

        // dd($user);  

        // $this->denyAccessUnlessGranted(Roles::ROLE_USER_STUDENT);
        // dd($this->getUser());
        $name = $user->firstName;
        $id = $user->getId();

        // return new Response($this->getUser());
        // return new Response('testing!!');
        return $this->json(array('id' => $id,'first_name'=> $name, 'email' => $user->email, 'last_name' => $user->lastName ), 200, ['token' => $token]);
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
