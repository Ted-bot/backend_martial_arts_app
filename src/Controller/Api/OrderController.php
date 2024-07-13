<?php

namespace App\Controller\Api;

use DateTime;
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
use App\Repository\SubscriptionTypeRepository;
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
        protected SubscriptionTypeRepository $subscriptionTypeRepo,
    )
    {}

    #[Route('/api/v1/order/', name: 'app_order', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $user = $this->userRepository->findOneBy([ 'id' => $this->getUser()]);
        $userLatestOrder = $this->shopOrderRepository->findOneBy(['ownedBy' => $user]);
        $orderLines = array();
        $orderTax = BigDecimal::zero();
        $orderTotalProductPrice= BigDecimal::zero();
        $currencyType = $this->currenyTypeRepo->findOneBy(['name' => 'EUR']);
        $symbol = Currencies::getSymbol($currencyType->getName());
        
        foreach( $userLatestOrder->getOrderLines() as $order ){
            $currentTime = new DateTime();
            $userSelectedProduct = $this->prRepo->findOneBy(['id' => $order->getProduct()->getId()]);
            $userProductTax = $this->prVatRepo->findOneBy(['product' => $userSelectedProduct->getId()]);
            $productVatAmount = $userProductTax->getVatAmount();
            $taxAmountTimesQty = BigDecimal::of($productVatAmount)->multipliedBy($order->getQty());
            $productPricePlusTotalTax = BigDecimal::of($order->getPrice())->plus($taxAmountTimesQty);
            
            $susbcriptionType = $this->subscriptionTypeRepo->findOneBy(['id' => $userSelectedProduct->getDuration()]);
            $subscriptionDuration = in_array($susbcriptionType->getDuration(),['month','no_duration']);
            $addMonthOrWeek = $subscriptionDuration !== true ? 'week' : 'month';
            
            $isProduct = $susbcriptionType->getDuration() !== 'month';
            $trailOrProduct = $subscriptionDuration == false ? 2 : 1;
            $setDurationProduct = $isProduct ? $trailOrProduct : $order->getQty();

            $orderLine = [
                'productName' => $order->getProduct()->getName(),
                'productDescr' => $order->getProduct()->getDescription(),
                'totalAmountOrderProduct' => $productPricePlusTotalTax,
                'totalProductPrice' => $order->getPrice(),
                'totalTaxPrice' => $taxAmountTimesQty,
                'productPrice' => $order->getProduct()->getPrice(),
                'productTaxPrice' => $productVatAmount,
                'percentageTax' => $userProductTax->getVatRate()->getProcent(),
                'quantity' => $order->getQty(),
                'productSubscription' => $susbcriptionType->getDuration(),
                'productSubscriptionStart' => $currentTime->format('Y-m-d'),
                'productSubscriptionEnd' => $currentTime->modify('+' . $setDurationProduct . ' ' . $addMonthOrWeek)->format('Y-m-d'),
            ];
            
            $orderLines[] = $orderLine;
            $orderTax = $orderTax->plus($orderLine['totalTaxPrice']);
            $orderTotalProductPrice = $orderTotalProductPrice->plus($orderLine['totalProductPrice']);
        }
        
        return new JsonResponse([
            'orderId' => $userLatestOrder->getId(),
            'curreny' => [
                'symbol' => $symbol,
                'name' => $currencyType->getName()
            ],
            'userId' => $user->getId(),
            'orderTotalProductPrice' => $orderTotalProductPrice->toScale(2, RoundingMode::UNNECESSARY),
            'orderTaxPrice' => $orderTax->toScale(2, RoundingMode::UNNECESSARY),
            'orderTotalAmount' => $userLatestOrder->getTotalAmount(),
            'lastOrder' =>  $orderLines
        ], 201);
    }
}
