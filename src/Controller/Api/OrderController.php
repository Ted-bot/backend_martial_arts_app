<?php

namespace App\Controller\Api;

use App\Entity\ShopOrder;
use App\Enum\MolliePaymentStatusEnum;
use DateTime;
use App\Class\Role;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\OrderLine;
use Brick\Math\BigDecimal;
use App\Entity\UserAddress;
use App\Dto\CustomerInfoDto;
use Brick\Math\RoundingMode;
use App\Enum\CountryTypeEnum;
use App\Entity\StatusTransfer;
use App\Enum\CurrencyTypeEnum;
use Mollie\Api\MollieApiClient;
use App\Dto\CreateMollieOrderDto;
use App\Enum\SubscriptionTypeEnum;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use App\Repository\CountryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Intl\Currencies;
use App\Enum\CountryToCurrencyTypeEnum;
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

class OrderController extends AbstractController
{
    private $mollie;

    public function __construct(
        protected UserRepository $userRepository,
        public ShopOrderRepository $shopOrderRepository,
        private OrderLineRepository $orderLinesRepo,
        protected ProductRepository $prRepo,
        protected ProductVatRepository $prVatRepo,
        // protected CurrencyTypeRepository $currenyTypeRepo,
        // protected SubscriptionTypeRepository $subscriptionTypeRepo,
        protected UserAddressRepository $userAddressRepo,
        protected AddressRepository $addressRepo,
        // protected CountryRepository $countryRepo,
        // private StatusTransfer $statusTransfer,
        private EntityManager $entityManager,
    )
    {
        // $this->mollie = new Mollie($this->getParameter('mollie.test'));
    }

    #[Route('/api/v1/order/create', name: 'app_create_order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    // public function payUserOrder(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $shoppingCart = $this->prRepo->findOneBy(['sku' => $request->getPayload()->get('sku')]);
        $user = $this->getUser();
        $user = $this->userRepository->findOneBy(['id' => $this->getUser()]);
        $userLatestOrder = $this->shopOrderRepository->findOneBy([
            'ownedBy' => $user,
            'orderStatus' => MolliePaymentStatusEnum::OPEN
        ]);

        // dd(['latestOrderStatus' => $userLatestOrder->getOrderStatus(), 'latestOrderDate' => $userLatestOrder->getOrderDate()]);
        
        // dd(['emptyLatestOrder' =>$userLatestOrder]);
        if($userLatestOrder !== null){
            // get Subscription Entity and stop if extension is still valid for more then one month to prevent headache later
            if($userLatestOrder->getOrderStatus() != MolliePaymentStatusEnum::OPEN){
                foreach($userLatestOrder->getOrderLines() as $orderLine){
    
                    if($orderLine->getProduct() === $shoppingCart->getId()){
                        $addOnToQty = $orderLine->getQty() + 1;
                        $orderLine->setQty($addOnToQty);
                        $addOnTotalAmount = BigDecimal::of($userLatestOrder->getTotalAmount())->plus($shoppingCart->getPrice());
                        $userLatestOrder->setTotalAmount($addOnTotalAmount);
    
                        $this->entityManager->persist($userLatestOrder);
                        $this->entityManager->persist($orderLine);
                        $this->entityManager->flush();
                    } else {
                        $addOneOrderLine = new OrderLine();
                        $addOneOrderLine->setShopOrder($userLatestOrder);
                        $addOneOrderLine->setProduct($shoppingCart);
                        $addOneOrderLine->setPrice($shoppingCart->getPrice());
                        
                        $addOnTotalAmount = BigDecimal::of($userLatestOrder->getTotalAmount())->plus($shoppingCart->getPrice());
                        $userLatestOrder->setTotalAmount($addOnTotalAmount);
    
                        $this->entityManager->persist($userLatestOrder);
                        $this->entityManager->persist($orderLine);
                        $this->entityManager->flush();
                    }
                }
            } elseif($userLatestOrder->getOrderStatus() === MolliePaymentStatusEnum::PROCESSING){
                return new JsonResponse(['message' => 'Your order is busy processing!']);
            }

            return new JsonResponse(['redirect' => '/payment'], 200);

            // dd(['foundProductInsidePreviousOrder'=>$foundProductInsidePreviousOrder]);
            // $newShopOrder = $userLatestOrder;
            
            // Check if shop order has same subscription in basket as previous then no need to contine go ahead to payment page
        } else {
            $newShopOrder = new ShopOrder();
        }
        
        
        $newOrderLine = new OrderLine();

        $newOrderLine->setProduct($shoppingCart);
        $newOrderLine->setShopOrder($newShopOrder);
        $newOrderLine->setPrice($shoppingCart->getPrice());
        $newOrderLine->setQty(1);

        $newShopOrder->setOwnedBy($this->getUser());
        $newShopOrder->setOrderStatus(MolliePaymentStatusEnum::OPEN);
        $newShopOrder->addOrderLine($newOrderLine);
        $newShopOrder->setTotalAmount($shoppingCart->getPrice());

        $this->entityManager->persist($newOrderLine);
        $this->entityManager->persist($newShopOrder);
        $this->entityManager->flush();

        return new JsonResponse(['redirect' => '/payment'], 200);
    }

    #[Route('/api/v1/order/address', name: 'app_order_address', methods: ['POST'])]
    public function orderAddress(
        #[MapRequestPayload] CustomerInfoDto $request,
        // EntityManager $entityManager,
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
        $addressId->setDefault(false);
        
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
        $address->setCountry(CountryTypeEnum::NL_CODE);

        $newUserAddress = new UserAddress();
        $newUserAddress->setAddress($address);
        $newUserAddress->setRelatedUser($user);
        $newUserAddress->setDefault(true);

        try {
            $this->entityManager->persist($addressId);
            $this->entityManager->persist($newUserAddress); // set prvious address Id on false
            $this->entityManager->persist($user); // update user Data
            $this->entityManager->persist($address); // update user Data            
            
            $this->entityManager->flush(); // update user Data
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

    #[Route('/api/v1/order/payment', name: 'app_order', methods: ['GET'])]
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

        $addressId = $this->userAddressRepo->findOneBy(['relatedUser' => $user->getId(), 'isDefault' => 'true']);

        // dd(['address'=>$addressId]);
        if($addressId !== null){
            $userAddress = $this->addressRepo->findOneBy(['id' => $addressId->getAddress()]);
            $userCountry = CountryTypeEnum::toString($userAddress->getCountry());
            $exchangeToCountry = CountryToCurrencyTypeEnum::getCurrencyType($userAddress->getCountry());
            $symbol = Currencies::getSymbol($exchangeToCountry);
        } else {
            $userAddress = false;
            $exchangeToCountry = false;
            $symbol = Currencies::getSymbol('EUR');
        }
        
        // $prodUrlNr = 0;
        // foreach( $userLatestOrder->getOrderLines() as $order ){
        //     $prodUrlNr++;
        //     $currentTime = new DateTime();
        //     $userSelectedProduct = $this->prRepo->findOneBy(['id' => $order->getProduct()->getId()]);
        //     $selctedProductTotalPrice = BigDecimal::of($userSelectedProduct->getPrice())->multipliedBy($order->getQty());
        //     $userProductTax = $this->prVatRepo->findOneBy(['product' => $userSelectedProduct->getId()]);
        //     $productVatAmount = BigDecimal::of($userProductTax->getVatAmount())->toScale(2, RoundingMode::UP);
        //     $taxAmountTimesQty = BigDecimal::of($productVatAmount)->multipliedBy($order->getQty());
        //     // $productPricePlusTotalTax = BigDecimal::of($order->getPrice())->plus($taxAmountTimesQty);
            
        //     $susbcriptionType = SubscriptionTypeEnum::tryFrom($userSelectedProduct->getDuration()->getValue());
        //     $subscriptionDuration = in_array($susbcriptionType->getValue(),['month','UNAVAILABLE']);
        //     $addMonthOrWeek = $subscriptionDuration !== true ? 'week' : 'month';
            
        //     $isProduct = $susbcriptionType !== SubscriptionTypeEnum::MONTH;
        //     $trailOrProduct = $subscriptionDuration == false ? 2 : 1;
        //     $setDurationProduct = $isProduct ? $trailOrProduct : $order->getQty();

        //     // dd(BigDecimal::ofUnscaledValue($taxAmountTimesQty)->toScale(2, RoundingMode::UP));
        //     // dd($taxAmountTimesQty);
        //     // dd(BigDecimal::of($taxAmountTimesQty)->toScale(2, RoundingMode::UP));
        //     $orderLine = [
        //         'sku' => $order->getProduct()->getSku(), // create sku
        //         'type' => 'store_credit',
        //         'description' => $order->getProduct()->getDescription(),
        //         'productUrl' => 'http://localhost:5173/product' . $prodUrlNr,
        //         'imageUrl' => 'http://localhost:5173/testimageUrl',
        //         'quantity' => $order->getQty(),
        //         'vatRate' => $userProductTax->getVatRate()->getProcent(),
        //         'unitPrice' => [
        //             'currency' => $exchangeToCountry ?: CurrencyTypeEnum::EUR,
        //             'value' => $order->getProduct()->getPrice()
        //         ],
        //         'totalAmount' => [
        //             'currency' => $order->getProduct()->getCurrencyType()->getId(),
        //             // 'currency' => $exchangeToCountry,
        //             'value' => $selctedProductTotalPrice
        //         ],
        //         // 'discountAmount' => [
        //         //     'currency' => $currencyType,
        //         //     'value' => '00.00',
        //         // ],
        //         'vatAmount' => [
        //             'currency' => $exchangeToCountry ?: CurrencyTypeEnum::EUR,
        //             'value' => $taxAmountTimesQty,
        //             // 'value' => BigDecimal::of($taxAmountTimesQty)->toScale(2, RoundingMode::HALF_UP),
        //         ],
        //         'productDetails' => [
        //             'totalProductCalculations' => [
        //                 'totalProductPrice' => $order->getPrice(),
        //                 'totalTaxPrice' => $taxAmountTimesQty,
        //             ],
        //             'subscriptionDetails' => [
        //                 'productSubscription' => $susbcriptionType->getValue(),
        //                 'productSubscriptionStart' => $currentTime->format('Y-m-d'),
        //                 'productSubscriptionEnd' => $currentTime->modify('+' . $setDurationProduct . ' ' . $addMonthOrWeek)->format('Y-m-d')
        //             ]
        //         ]
        //     ];
            
        //     // $getOrderLineProductDetailsKey = array_key_last($orderLine);            
        //     $orderTax = $orderTax->plus($orderLine['productDetails']['totalProductCalculations']['totalTaxPrice']);
        //     $orderTotalProductPrice = $orderTotalProductPrice->plus($orderLine['productDetails']['totalProductCalculations']['totalProductPrice']);
            
        //     $orderLines[] = $orderLine;
        // }
        
        // unset($orderLine[$getOrderLineProductDetailsKey]);
        // dd([
        //     // 'unset' => $orderLine[$getOrderLineProductDetailsKey],
        //     'allOrderlines' => $orderLine
        // ]);
        
        return new JsonResponse(['test' => 'it WOrks!'], 200);
        // return new JsonResponse([
        //     'description' => 'order ' . $userLatestOrder->getId() . ' Black Dragon M.A.',
        //     'address' => ($userAddress !== false) ? [
        //         'id' => $userAddress->getId(),
        //         'addressLine' => $userAddress->getAddressLine(),
        //         'unitNumber' => $userAddress->getUnitNumber(),
        //         'streetNumber' => $userAddress->getStreetNumber(),
        //         'postalCode' => $userAddress->getPostalCode(),
        //         'reactCityNr' => $user->getLibReactCity(),
        //         'reactStateNr' => $user->getLibReactState(),
        //         'country' => $userCountry,
        //     ] : [],
        //     'orderId' => $userLatestOrder->getId(),
        //     'curreny' => [
        //         'symbol' => $symbol,
        //         'name' => $exchangeToCountry
        //     ],
        //     'locale' => CountryTypeEnum::NL_LOCALE,
        //     'user' => [
        //         'id' => $user->getId(),
        //         'email' => $user->getEmail(),
        //         'phoneNumber' => $user->getPhoneNumber(),
        //         'firstAndLastName' => $user->getFirstName() . ' ' . $user->getLastName(),
        //         'city' => $user->getLocation(),
        //         // 'consumerDateOfBirth' => $user->getDateOfBirth(),
        //     ],
        //     'orderTotalProductPrice' => $orderTotalProductPrice->toScale(2, RoundingMode::UP),
        //     'orderTaxPrice' => $orderTax->toScale(2, RoundingMode::UP),
        //     'amount' => [
        //         'value' => $userLatestOrder->getTotalAmount(),
        //         'currency' => $exchangeToCountry,
        //     ],
        //     'lines' => $orderLines
        // ], 201);
    }

    #[Route('/api/v1/payment', name: 'app_payment', methods: ['POST'])]
    public function payUserOrder(#[MapRequestPayload] CreateMollieOrderDto $request): JsonResponse
    // public function payUserOrder(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $mollie = new MollieApiClient();
        // $statusTransfer = new StatusTransfer();
        $mollie->setApiKey($this->getParameter('mollie.test'));
        $shopOrder = $this->shopOrderRepository->findOneBy(['id' => $request->order_id]);
        
        $newUserOrder = [
            "amount" => $request->amount,
            "billingAddress" => $request->billingAddress,
            "shippingAddress" => $request->shippingAddress,
            "metadata" => $request->metadata,
            "description" => $request->description,
            "locale" => $request->locale,
            "redirectUrl" => $request->redirectUrl,
            "webhookUrl" => $request->webhookUrl . '/webhook/MollieDirectPayment',
            "method" => $request->method,
            "lines" => $request->lines
        ];
        
        $createPayment = $mollie->payments->create($newUserOrder);
        $transferId = $createPayment->id;

        // $statusTransfer->setUserOrder($shopOrder);
        // $statusTransfer->setTransferId($transferId);

        // $this->entityManager->persist($statusTransfer);
        // $this->entityManager->flush();

        return new JsonResponse('test_payment', 200);
    }
}
