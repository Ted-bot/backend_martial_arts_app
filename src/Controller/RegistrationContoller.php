<?php

namespace App\Controller;

use App\Dto\requestDto;
use App\Dto\CreateUserDto;
use App\Request\CreateUserRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            // 'date_of_birth' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
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
    public function v2Create(CreateUserRequest $request): JsonResponse
    {
        return $this->json([
            'response' => 'ok',
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'phone' => $request->phoneNumber,
            'gender' => $request->gender,
            // 'date_of_birth' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            'date_of_birth' => $request->dateOfBirth,
            'location' => $request->location,
            'password' => $request->password,
            'conversion' => $request->conversion,
            'headers' => [
                'Content-Type' => $request
                    ->getRequest()
                    ->headers
                    ->get('Content-Type')
                ,
            ],
        ]);
    }
}
