<?php

namespace App\Controller\Api;

use App\Repository\CountryRepository;
use DateTime;
use App\Class\Role;
use App\Entity\OrderLine;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Intl\Currencies;
use App\Repository\OrderLineRepository;
use App\Repository\ShopOrderRepository;
use App\Repository\ProductVatRepository;
use App\Repository\UserAddressRepository;
use App\Repository\CurrencyTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SubscriptionTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\MollieClient as Mollie;

class OrderController extends AbstractController
{
    private $mollie;
    private $billingAddress;
    private $shippingAddress;
    private $metadata;
    private $locale;
    private $consumerDateOfBirth;
    private $orderNumber;
    private $redirectUrl;
    private $webhookUrl;
    private $method;
    private $lines;


    public function __construct(
        protected UserRepository $userRepository,
        public ShopOrderRepository $shopOrderRepository,
        private OrderLineRepository $orderLinesRepo,
        protected ProductRepository $prRepo,
        protected ProductVatRepository $prVatRepo,
        protected CurrencyTypeRepository $currenyTypeRepo,
        protected SubscriptionTypeRepository $subscriptionTypeRepo,
        protected UserAddressRepository $userAddressRepo,
        protected AddressRepository $addressRepo,
        protected CountryRepository $countryRepo,
        // protected Mollie $mollie
    )
    {
        // $this->mollie = new Mollie($this->getParameter('mollie.test'));
    }

    #[Route('/api/v1/order', name: 'app_order', methods: ['GET'])]
    public function order(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        // return ;

    }

    #[Route('/api/v1/payment', name: 'app_payment', methods: ['GET'])]
    public function payment(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $user = $this->userRepository->findOneBy([ 'id' => $this->getUser()]);
        $userLatestOrder = $this->shopOrderRepository->findOneBy(['ownedBy' => $user]);
        $orderLines = array();
        $orderTax = BigDecimal::zero();
        $orderTotalProductPrice= BigDecimal::zero();
        $currencyType = $this->currenyTypeRepo->findOneBy(['name' => 'EUR']);
        $symbol = Currencies::getSymbol($currencyType->getName());
        $addressId = $this->userAddressRepo->findOneBy(['relatedUser' => $user->getId(), 'isDefault' => 'true']);
        $userAddress = $this->addressRepo->findOneBy(['id' => $addressId->getAddress()]);
        $userCountry = $this->countryRepo->findOneBy(['id' => $userAddress->getCountry()]);


        $prodUrlNr = 0;
        foreach( $userLatestOrder->getOrderLines() as $order ){
            $prodUrlNr++;
            $currentTime = new DateTime();
            $userSelectedProduct = $this->prRepo->findOneBy(['id' => $order->getProduct()->getId()]);
            $selctedProductTotalPrice = BigDecimal::of($userSelectedProduct->getPrice())->multipliedBy($order->getQty());
            $userProductTax = $this->prVatRepo->findOneBy(['product' => $userSelectedProduct->getId()]);
            $productVatAmount = $userProductTax->getVatAmount();
            $taxAmountTimesQty = BigDecimal::of($productVatAmount)->multipliedBy($order->getQty());
            // $productPricePlusTotalTax = BigDecimal::of($order->getPrice())->plus($taxAmountTimesQty);
            
            $susbcriptionType = $this->subscriptionTypeRepo->findOneBy(['id' => $userSelectedProduct->getDuration()]);
            $subscriptionDuration = in_array($susbcriptionType->getDuration(),['month','no_duration']);
            $addMonthOrWeek = $subscriptionDuration !== true ? 'week' : 'month';
            
            $isProduct = $susbcriptionType->getDuration() !== 'month';
            $trailOrProduct = $subscriptionDuration == false ? 2 : 1;
            $setDurationProduct = $isProduct ? $trailOrProduct : $order->getQty();

            $orderLine = [
                'sku' => $order->getProduct()->getSku(), // create sku
                'name' => $order->getProduct()->getName(),
                'productUrl' => 'http://localhost:5173/product' . $prodUrlNr,
                'imageUrl' => 'testimageUrl',
                'quantity' => $order->getQty(),
                'vatRate' => $userProductTax->getVatRate()->getProcent(),
                'unitPrice' => [
                    'currency' => $currencyType->getName(),
                    'value' => $order->getProduct()->getPrice()
                ],
                'totalAmount' => [
                    'currency' => $currencyType->getName(),
                    'value' => $selctedProductTotalPrice
                ],
                'discountAmount' => [
                    'currency' => $currencyType->getName(),
                    'value' => '00.00',
                ],
                'vatAmount' => [
                    'currency' => $currencyType->getName(),
                    'value' => $taxAmountTimesQty,
                ],
                'productDetails' => [
                    'totalProductCalculations' => [
                        'totalProductPrice' => $order->getPrice(),
                        'totalTaxPrice' => $taxAmountTimesQty,
                    ],
                    'subscriptionDetails' => [
                        'productSubscription' => $susbcriptionType->getDuration(),
                        'productSubscriptionStart' => $currentTime->format('Y-m-d'),
                        'productSubscriptionEnd' => $currentTime->modify('+' . $setDurationProduct . ' ' . $addMonthOrWeek)->format('Y-m-d')
                    ]
                ]
            ];
            
            $getOrderLineProductDetailsKey = array_key_last($orderLine);            
            $orderTax = $orderTax->plus($orderLine['productDetails']['totalProductCalculations']['totalTaxPrice']);
            $orderTotalProductPrice = $orderTotalProductPrice->plus($orderLine['productDetails']['totalProductCalculations']['totalProductPrice']);
            
            $orderLines[] = $orderLine;
        }

        unset($orderLine[$getOrderLineProductDetailsKey]);
        $this->lines[] = $orderLine;

        $this->billingAddress = [
            'streetAndNumber' => $userAddress->getAddressLine() .' '. $userAddress->getStreetNumber() .' '. $userAddress->getUnitNumber(),
            'postalCode' => $userAddress->getPostalCode(),
            'city' => $userAddress->getCity() ? $userAddress->getCity() : ($user->getLocation() ? $user->getLocation() : '' ),
            'country' => $userCountry->getCode(),
            'givenName' => $user->getFirstName(),
            'familyName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];

        $this->consumerDateOfBirth = $user->getDateOfBirth();
        $this->locale = $userCountry->getLocale();
        
        return new JsonResponse([
            'address' => [
                'id' => $userAddress->getId(),
                'streetName' => $userAddress->getAddressLine(),
                'unitNumber' => $userAddress->getUnitNumber(),
                'streetNumber' => $userAddress->getStreetNumber(),
                'postalCode' => $userAddress->getPostalCode(),
                'reactCityNr' => $user->getLibReactCity(),
                'reactStateNr' => $user->getLibReactState(),
                'county' => $userCountry->getCode(),
            ],
            'orderId' => $userLatestOrder->getId(),
            'curreny' => [
                'symbol' => $symbol,
                'name' => $currencyType->getName()
            ],
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'phoneNumber' => $user->getPhoneNumber(),
                'firstAndLastName' => $user->getFirstName() . ' ' . $user->getLastName(),
            ],
            'orderTotalProductPrice' => $orderTotalProductPrice->toScale(2, RoundingMode::UNNECESSARY),
            'orderTaxPrice' => $orderTax->toScale(2, RoundingMode::UNNECESSARY),
            'amount' => [
                'value' => $userLatestOrder->getTotalAmount(),
                'currency' => $currencyType->getName(),
            ],
            'lines' => $orderLines
        ], 201);
    }

    #[Route('/api/v1/payment')]
    public function payUserOrder(Request $request)
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $mollie = new Mollie($this->getParameter('mollie.test'));

        dd($mollie);

    }
}
