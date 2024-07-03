<?php

namespace App\Controller\Api;

use App\Class\Role;
use App\Entity\OrderLine;
use App\Repository\OrderLineRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductVatRepository;
use App\Repository\ShopOrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    public function __construct(
        protected UserRepository $userRepository,
        public ShopOrderRepository $shopOrderRepository,
        private OrderLineRepository $orderLinesRepo,
        protected ProductRepository $prRepo,
        protected ProductVatRepository $prVatRepo,
    )
    {}

    #[Route('/api/v1/order/', name: 'app_order', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $user = $this->userRepository->findOneBy([ 'id' => $this->getUser()]);

        // dd($user->getId());

        // $shopOrderQueryLatestOrder = $this->shopOrderRepository->findUserLatestOrder($user);
        $userLatestOrder = $this->shopOrderRepository->findOneBy(['ownedBy' => $user]);
        // $userOrderLines = $this->orderLinesRepo->findOneBy(['shopOrder' => $userLatestOrder->getId()]);

        $userLatestOrderId = $userLatestOrder->getId();
        $userOrderLines = $this->orderLinesRepo->findOneBy(['shopOrder' => $userLatestOrderId]);

        //need to be dynamic
        // $userSelectedProduct = $this->prRepo->findOneBy(['id' => $userOrderLines->getProduct()->getId()]);
        // $userProductTax = $this->prVatRepo->findOneBy(['id' => $userSelectedProduct->getId()]);

        // function showUserOrder($orderId, $orderLines){
        //     $selectedProduct = $this->prRepo->findOneBy(['id' => $orderLines]);
        //     $productTax = $this->prVatRepo->findOneBy(['id' =>$selectedProduct->getId()]);
        // }
        $orderLines = array();

        foreach( $userLatestOrder->getOrderLines() as $order ){
            $userSelectedProduct = $this->prRepo->findOneBy(['id' => $order->getProduct()->getId()]);
            $userProductTax = $this->prVatRepo->findOneBy(['id' => $userSelectedProduct->getId()]);

            $orderLine = [
                'productName' => $order->getProduct()->getName(),
                'totalAmountOrderProduct' => $order->getPrice() + ($userProductTax->getVatAmount() * $order->getQty()),
                'totalProductPrice' => $order->getPrice(),
                'totalTaxPrice' => $userProductTax->getVatAmount() * $order->getQty(),
                'productPrice' => $order->getProduct()->getPrice(),
                'productTaxPrice' => $userProductTax->getVatAmount(),
                'percentageTax' => $userProductTax->getVatRate()->getProcent(),
                'quantity' => $order->getQty(),
            ];

            $orderLines[] = $orderLine;
        }

        return new JsonResponse([
            'orderId' => $userLatestOrder->getId(),
            'userId' => $user->getId(),
            'orderTotalPrice' => $userLatestOrder->getTotalAmount(),
            'lastOrder' =>  $orderLines
        ], 201);
    }
}
