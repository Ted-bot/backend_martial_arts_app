<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Encoder\NixillaJWTEncoder;
use App\Dto\User\CreateUserDto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;

class RegistrationContoller extends AbstractController
{
    #[Route(
        '/api/v1/register', 
        name: 'api_register_new_user_v1',
        methods: 'POST'
    )]
    public function v1Create(#[MapRequestPayload] CreateUserDto $createUser): JsonResponse
    {
        return $this->json([
            'response' => 'ok',
            'firstName' => $createUser->first_name,
            'lastName' => $createUser->last_name,
            'email' => $createUser->email,
            'phone' => $createUser->phone_number,
            'gender' => $createUser->gender,
            'date_of_birth' => $createUser->date_of_birth,
            'location' => $createUser->location,
            'password' => $createUser->password,
            'conversion' => $createUser->conversion            
        ]);
    }

    #[Route(
        '/api/v2/register', 
        name: 'api_register_new_user_v2',
        methods: 'POST',
        )]
    public function v2Create(
        CreateUserDto $request,
        // #[MapRequestPayload] CreateUserDto $request,
        EntityManager $entityManager,
        JWTTokenManagerInterface $JWTManager,
        User $user,
        UserPasswordHasherInterface $passwordHasher
        ): JsonResponse
    {
        $user->createNewUserObj($request);

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );

        $user->setPassword($hashedPassword);

        try{
            $entityManager->persist($user);
            $entityManager->flush();
        } catch(UniqueConstraintViolationException $e){
            $sqlState = 0;
            $sqlState = $e->getSQLState();

            $field = [];
            $message = 'Please check your input and make sure email and phone are unique to our database';

            if($e->getSQLState() == 23505){
                array_push($field, 'email');
                $message = 'Your email address is known to our database, please reset password if you forgot';
            }

            return $this->json([
                'errors' => [
                    'error' => $e->getMessage(),
                    'property' => $field,
                    'sql_state' => $sqlState,
                    'message' => $message
                ]
            ], 
            400,
            [
                'Content-Type' =>  $request->getRequest()->headers->get('Content-Type')
            ]);
        }

        return new JsonResponse(['token' => $JWTManager->create($user)], 
        200,
        [
            'Content-Type' =>  $request->getRequest()->headers->get('Content-Type')
        ]);
    }

    // #[Route(
    //     '/api/v1/login', 
    //     name: 'api_login',
    //     methods: 'POST',
    //     )]
    // public function v1Login(
    //     #[CurrentUser] ?User $user,
    //     AuthenticationSuccessHandler $authenticationSuccessHandler,
    //     AuthenticationUtils $authenticationUtils
    //     ): Response
    // {
    //     if (null === $user) {
    //         return new Response($authenticationUtils->getLastAuthenticationError(),
    //         Response::HTTP_UNAUTHORIZED);
    //     }

    //     return $authenticationSuccessHandler->handleAuthenticationSuccess($user);
    // }

    #[Route(
        '/api/logout', 
        name: 'api_logout'
        )]
    public function v1Logout()
    {
        throw new \Exception('Never user this method!');
    }

    #[Route(
        '/api/v1/reset-password', 
        name: 'api_reset_password'
        )]
    public function v1ResetPassword()
    {

    }
}
