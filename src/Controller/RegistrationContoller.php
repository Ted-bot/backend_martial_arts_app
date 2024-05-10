<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistrationContoller extends AbstractController
{
    #[Route('/api/register', name: 'app_registration_contoller')]
    public function index(Request $request): JsonResponse
    {
        $test = [
            'name' => 'TestHttpRequest',
            'content' => $request->getContent()
        ];

        // dd($test);
        return new JsonResponse(
            $test,
            200
        );
    }
}
