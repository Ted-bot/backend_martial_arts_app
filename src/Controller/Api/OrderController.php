<?php

namespace App\Controller\Api;

use App\Class\Role;
use App\Entity\OrderLine;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Intl\Currencies;
use App\Repository\OrderLineRepository;
use App\Repository\ShopOrderRepository;
use App\Repository\ProductVatRepository;
use App\Repository\CurrencyTypeRepository;
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
        protected CurrencyTypeRepository $currenyTypeRepo,
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
        $orderTax = BigDecimal::zero();
        $orderTotalProductPrice= BigDecimal::zero();
        $currencyType = $this->currenyTypeRepo->findOneBy(['name' => 'EUR']);
        $symbol = Currencies::getSymbol($currencyType->getName());
        
        foreach( $userLatestOrder->getOrderLines() as $order ){
            $userSelectedProduct = $this->prRepo->findOneBy(['id' => $order->getProduct()->getId()]);
            $userProductTax = $this->prVatRepo->findOneBy(['id' => $userSelectedProduct->getId()]);
            $taxAmountTimesQty = BigDecimal::of($userProductTax->getVatAmount())->multipliedBy($order->getQty());
            
            $orderLine = [
                'productName' => $order->getProduct()->getName(),
                'productDescr' => $order->getProduct()->getDescription(),
                'totalAmountOrderProduct' => BigDecimal::of($order->getPrice())->plus($taxAmountTimesQty),
                'totalProductPrice' => $order->getPrice(),
                'totalTaxPrice' => $userProductTax->getVatAmount() * $order->getQty(),
                'productPrice' => $order->getProduct()->getPrice(),
                'productTaxPrice' => $userProductTax->getVatAmount(),
                'percentageTax' => $userProductTax->getVatRate()->getProcent(),
                'quantity' => $order->getQty(),
            ];
            
            $orderLines[] = $orderLine;
            $orderTax = $orderTax->plus($orderLine['totalTaxPrice']);
            $orderTotalProductPrice = $orderTotalProductPrice->plus($orderLine['totalProductPrice']);
        }
        
        return new JsonResponse([
            'orderId' => $userLatestOrder->getId(),
            'curreny' => [
                'symbol' => $symbol,
                'name' => $currencyType->getName()],
            'userId' => $user->getId(),
            'orderTotalProductPrice' => $orderTotalProductPrice->toScale(2, RoundingMode::UNNECESSARY),
            'orderTaxPrice' => $orderTax->toScale(2, RoundingMode::UNNECESSARY),
            'orderTotalAmount' => $userLatestOrder->getTotalAmount(),
            'lastOrder' =>  $orderLines
        ], 201);
    }
}
