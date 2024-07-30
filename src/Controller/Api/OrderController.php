<?php

namespace App\Controller\Api;

use DateTime;
use App\Class\Role;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\OrderLine;
use Brick\Math\BigDecimal;
use App\Entity\UserAddress;
use App\Dto\CustomerInfoDto;
use Brick\Math\RoundingMode;
use Mollie\Api\MollieApiClient;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use App\Repository\CountryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Intl\Currencies;
use App\Repository\OrderLineRepository;
use App\Repository\ShopOrderRepository;
use App\Service\MollieClient as Mollie;
use App\Repository\ProductVatRepository;
use App\Repository\UserAddressRepository;
use App\Repository\CurrencyTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SubscriptionTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Dto\CreateMollieOrderDto;

class OrderController extends AbstractController
{
    private $mollie;

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

    #[Route('/api/v1/order/address', name: 'app_order_address', methods: ['POST'])]
    public function orderAddress(
        #[MapRequestPayload] CustomerInfoDto $request,
        EntityManager $entityManager,
        Address $address,
    ): JsonResponse
    // public function orderAddress(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);
        
        $splitFirstAndLastName = explode(" ", $request->firstAndLastName);

        /** @var User User Object */
        $user = $this->userRepository->findOneBy(['id' => $this->getUser()]);

        /** @var UserAddress UserAddres Object */
        $addressId = $this->userAddressRepo->findOneBy(['relatedUser' => $user->getId(), 'isDefault' => 'true']);
        
        $userAddress = $this->addressRepo->findOneBy(['id' => $addressId->getAddress()]);

        $addressId->setDefault(false);
        
        $userCountry = $this->countryRepo->findOneBy(['id' => $userAddress->getCountry()]);

        $user->setFirstName($splitFirstAndLastName[0]);
        $user->setLastName($splitFirstAndLastName[1]);
        $user->setEmail($request->email);
        $user->setPhoneNumber($request->phoneNumber);
        $user->setLibReactCity($request->reactCityNr);
        $user->setLibReactState($request->reactStateNr);
        $user->setLocation($request->location);

        $address->setUnitNumber($request->unitNumber);
        $address->setStreetNumber($request->streetNumber);
        $address->setAddressLine($request->addressLine);
        $address->setCity($request->location); // set city
        $address->setRegion($request->region); // set state
        $address->setPostalCode($request->postalCode);
        $address->setCountry($userCountry);

        $newUserAddress = new UserAddress();
        $newUserAddress->setAddress($address);
        $newUserAddress->setRelatedUser($user);
        $newUserAddress->setDefault(true);

        try {
            $entityManager->persist($addressId);
            $entityManager->persist($newUserAddress); // set prvious address Id on false
            $entityManager->persist($user); // update user Data
            $entityManager->persist($address); // update user Data            
            
            $entityManager->flush(); // update user Data
        } catch(UniqueConstraintViolationException $e){

            $message = 'Couldnt set new Address and Update User';

            return new JsonResponse([
                'errors' => [
                    'error' => $e->getMessage(),
                    'message' => $message
                ]
            ], 
            400,
            [
                'Content-Type' =>  "application/json"
            ]);
        }

        return new JsonResponse('Order Addres & User Had been updated!', 
        200,
        [
            'Content-Type' =>  "application/json"
        ]);
    }

    #[Route('/api/v1/order', name: 'app_order', methods: ['GET'])]
    public function order(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        // $mollie = new Mollie($this->getParameter('mollie.test'));

        $user = $this->userRepository->findOneBy(['id' => $this->getUser()]);
        $userLatestOrder = $this->shopOrderRepository->findOneBy(['ownedBy' => $user]);
        // $mollie->consumerDateOfBirth = $user->getDateOfBirth();
        // $mollie->orderNumber = $userLatestOrder->getId();

        $orderLines = array();
        $orderTax = BigDecimal::zero();
        $orderTotalProductPrice= BigDecimal::zero();
        $currencyType = $this->currenyTypeRepo->findOneBy(['name' => 'EUR']);
        $symbol = Currencies::getSymbol($currencyType->getName());
        $addressId = $this->userAddressRepo->findOneBy(['relatedUser' => $user->getId(), 'isDefault' => 'true']);
        $userAddress = $this->addressRepo->findOneBy(['id' => $addressId->getAddress()]);
        $userCountry = $this->countryRepo->findOneBy(['id' => $userAddress->getCountry()]);
        // set property
        // $mollie->locale = $userCountry->getLocale();

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

        // $mollie->setlines($orderLine);
        // $mollie->setAmount([
        //         'value' => $userLatestOrder->getTotalAmount(),
        //         'currency' => $currencyType->getName(),
        //     ]);

        // $mollie->setBillingAddress([
        //     'streetAndNumber' => $userAddress->getAddressLine() .' '. $userAddress->getStreetNumber() .' '. $userAddress->getUnitNumber(),
        //     'postalCode' => $userAddress->getPostalCode(),
        //     'city' => $userAddress->getCity() ? $userAddress->getCity() : ($user->getLocation() ? $user->getLocation() : '' ),
        //     'country' => $userCountry->getCode(),
        //     'givenName' => $user->getFirstName(),
        //     'familyName' => $user->getLastName(),
        //     'email' => $user->getEmail(),
        // ]);

        // $mollie->setConsumerDateOfBirth($user->getDateOfBirth());
        // $mollie->setLocale($userCountry->getLocale());
        
        return new JsonResponse([
            'address' => [
                'id' => $userAddress->getId(),
                'addressLine' => $userAddress->getAddressLine(),
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
            'locale' => $userCountry->getLocale(),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'phoneNumber' => $user->getPhoneNumber(),
                'firstAndLastName' => $user->getFirstName() . ' ' . $user->getLastName(),
                'city' => $user->getLocation(),
                'consumerDateOfBirth' => $user->getDateOfBirth(),
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

    #[Route('/api/v1/payment', name: 'app_payment', methods: ['POST'])]
    public function payUserOrder(#[MapRequestPayload] CreateMollieOrderDto $request): JsonResponse
    // public function payUserOrder(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        // dd(['content' => $request->getContent(), 'paymentMethod' => $request->getPayload()->get('paymentMethod'), 'request' => $request]);
        dd($request);
        // dd($request->getContent());

        $mollie = new MollieApiClient();
        $mollie->setApiKey($this->getParameter('mollie.test'));

        // $confirmedUserOrder = $request->getContent();

        $order = $mollie->orders->create([
            // "amount" => $this->amount,
            // "billingAddress" => $this->billingAddress,
            // "shippingAddress" => $this->billingAddress,
            // "metadata" => '',
            // "consumerDataOfBirth" => $this->consumerDateOfBirth,
            // "locale" => $this->locale,
            // "orderNumber" => $this->orderNumber,
            // "redirectUrl" => "https://your_domain.com/return?some_other_info=foo",
            // "webhookUrl" => "https://your_domain.com/webhook",
            // "method" => "ideal",
            // "lines" => $this->lines
        ]);

        // dd($order);

        return new JsonResponse('test_payment', 200);

    }
}
