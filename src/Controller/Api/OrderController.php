<?php

namespace App\Controller\Api;

use App\Class\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ShopOrderRepository;

class OrderController extends AbstractController
{
    public function __construct(
        private ShopOrderRepository $shopOrderRepository
    )
    {}

    #[Route('/api/v1/order/', name: 'app_order', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $user = $this->getUser();

        // dd($user->getId());

        $userLatestOrder = $this->shopOrderRepository->findUserLatestOrder($user);

        
        return new JsonResponse([ 'order' => $userLatestOrder], 201);
    }
}
