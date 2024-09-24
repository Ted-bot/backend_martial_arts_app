<?php

namespace App\Controller\Api;

use App\Dto\CreateMollieOrderRequest;
use App\Entity\ShopOrder;
use App\Enum\MolliePaymentStatusEnum;
use App\Message\Command\CreateSubscriptionMessage;
use App\Repository\StatusTransferRepository;
use App\Request\CustomerInfoRequest;
use App\Service\MollieClientHelper;
use App\Service\OrderCalulator;
use App\Service\SubscriptionUUID;
use DateTime;
use App\Class\Role;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\OrderLine;
use App\Entity\Subscription;
use App\Entity\UserAddress;
use Brick\Math\BigDecimal;
use App\Dto\CustomerInfoDto;
use Brick\Math\RoundingMode;
use App\Enum\CountryTypeEnum;
use App\Entity\StatusTransfer;
use App\Enum\CurrencyTypeEnum;
use app\Enum\SubscriptionLengthTypeEnum;
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
use Symfony\Component\Messenger\MessageBusInterface;
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
        private SubscriptionUUID $subscriptionUUID,
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
        private StatusTransferRepository $statusTransferRepo,
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
        // $user = $this->userRepository->findOneBy(['id' => $this->getUser()]);
        $userLatestOrder = $this->shopOrderRepository->findOneBy([
            'orderOwnedBy' => $user,
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
                return new JsonResponse(['message' => 'Your order is busy processing!'], 201);
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

        $newShopOrder->setOrderOwnedBy($this->getUser());
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
        CustomerInfoRequest $request,
        // #[MapRequestPayload] CustomerInfoDto $request,
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
        
        if($addressId){
            $addressId->setDefault(false);
        } 
        // else {
        //     $addressId = new Address();
        // }
        
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
            if($addressId){
                $this->entityManager->persist($addressId);
            }
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

        $molliehelper = new MollieClientHelper(); 

        $user = $this->userRepository->findOneBy(['id' => $this->getUser()]);
        $userLatestOrder = $this->shopOrderRepository->findOneBy(['orderOwnedBy' => $user]);        
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
        
        $prodUrlNr = 0;
        foreach( $userLatestOrder->getOrderLines() as $order ){
            $prodUrlNr++;
            $userSelectedProduct = $this->prRepo->findOneBy(['id' => $order->getProduct()->getId()]);
            $userProductTax = $this->prVatRepo->findOneBy(['product' => $userSelectedProduct->getId()]);
            $molliehelper->createOrderLine($order, $prodUrlNr, $userProductTax, $exchangeToCountry,$userSelectedProduct);
        }
        
        return new JsonResponse([
            'description' => 'order ' . $userLatestOrder->getId() . ' Black Dragon M.A.',
            'address' => ($userAddress !== false) ? [
                'id' => $userAddress->getId(),
                'addressLine' => $userAddress->getAddressLine(),
                'unitNumber' => $userAddress->getUnitNumber(),
                'streetNumber' => $userAddress->getStreetNumber(),
                'postalCode' => $userAddress->getPostalCode(),
                'reactCityNr' => $user->getLibReactCity(),
                'reactStateNr' => $user->getLibReactState(),
                'country' => $userCountry,
            ] : [],
            'orderId' => $userLatestOrder->getId(),
            'curreny' => [
                'symbol' => $symbol,
                'name' => $exchangeToCountry ?: CurrencyTypeEnum::EUR 
            ],
            'locale' => CountryTypeEnum::NL_LOCALE,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'phoneNumber' => $user->getPhoneNumber(),
                'firstAndLastName' => $user->getFirstName() . ' ' . $user->getLastName(),
                'city' => $user->getLocation(),
            ],
            'orderTotalProductPrice' => $molliehelper->orderTotalProductPrice->toScale(2, RoundingMode::UP),
            'orderTaxPrice' => $molliehelper->orderTax->toScale(2, RoundingMode::UP),
            'amount' => [
                // 'value' => $userLatestOrder->getTotalAmount(),
                'value' => $molliehelper->getOrderTotalProductPrice(),
                'currency' => $exchangeToCountry ?: CurrencyTypeEnum::EUR,
            ],
            'lines' => $molliehelper->lines
        ], 201);
    }

    #[Route('/api/v1/payment', name: 'app_payment', methods: ['POST'])]
    public function payUserOrder(#[MapRequestPayload] CreateMollieOrderDto $request, MessageBusInterface $messageBus): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $molliehelper = new MollieClientHelper(); 
        $mollie = new MollieApiClient();
        $mollie->setApiKey($this->getParameter('mollie.test'));            
        $statusTransfer = new StatusTransfer();
        
        $request->iban !== '' ?? $molliehelper->setSubscription(true);
        $newUserOrder = $molliehelper->setupOrderToPay($request)->getOrderToPay();

        /** @var ShopOrder $shopOrder Object */
        $shopOrder = $this->shopOrderRepository->findOneBy(['orderOwnedBy' => $this->getUser()]);

        /** @var StatusTransfer $knownMollieCustomer Object */
        $knownMollieCustomer = $this->statusTransferRepo->findOneBy(['userOrder' => $shopOrder->getId()]);

        // check if user has selected a subscription
        if($knownMollieCustomer->getCustomer() != null){
            $mollieCustomerId = $knownMollieCustomer->getCustomer();
            $customer = $mollie->customers->get($knownMollieCustomer->getCustomer());
        } else {
            $newCustomer = [
                "name" => $request->billingAddress->givenName,
                "email" => $request->billingAddress->email,
                "locale" => $request->locale,
                "metadata" => $request->metadata,
                // "testmode" => true,
            ];
            $customer = $mollie->customers->create($newCustomer);
            $mollieCustomerId = $customer->id;

            if($molliehelper->subscription) {
                $customer->createMandate([
                    "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
                    "consumerAccount" => $request->iban, // NL34ABNA0243341423
                    "consumerName" => $request->billingAddress->givenName . ' ' . $request->billingAddress->familyName
                ]);
            }
        }

        //Now make payment  error ApiException
        $createPayment = $customer->createPayment($newUserOrder); // error
        $transferId = $createPayment->id;            

        $statusTransfer->setUserOrder($shopOrder);
        $statusTransfer->setTransferId($transferId);
        $statusTransfer->setCustomer($mollieCustomerId);

        $this->entityManager->persist($statusTransfer);
        $this->entityManager->flush();        
        
        /*
        * Generate a unique subscription id for this example. It is important to include this unique attribute
        * in the webhookUrl (below) so new payments can be associated with this subscription.
        */
        if($molliehelper->subscription){
            
            $subscriptionLength = $request->subscriptionDetail->subscriptionLength;
            $subscriptionMonthOrWeek = $request->subscriptionDetail->subscriptionTimeUnit;
            $subscriptionAmount = $request->subscriptionDetail->subscriptionAmount;
            
            $subscriptionId = (new SubscriptionUUID)->create();
            // $subscriptionMessage = new CreateSubscriptionMessage($subscriptionId);
            // $messageBus->dispatch($subscriptionMessage);
            
            $subscription = new Subscription();
            $subscriptionLengthConvertToEnum = SubscriptionLengthTypeEnum::from($subscriptionLength);
            
            $subscription->setUuid($subscriptionId);
            $subscription->setSubscriptionOwnedBy($this->getUser());
            $subscription->setStatus(MolliePaymentStatusEnum::OPEN);
            $subscription->setTransferId(null); // webhook also setUpdateAt
            $subscription->setAmount($subscriptionAmount);
            $subscription->setDuration($subscriptionLengthConvertToEnum); //SubscriptionLengthTypeEnum
            $subscription->setDateEnd('+' . $subscriptionLength . ' ' . $subscriptionMonthOrWeek); //SubscriptionLengthTypeEnum    
                
            $customer->createSubscription([
                "amount" => [
                    "value" => $subscriptionAmount, // You must send the correct number of decimals, thus we enforce the use of strings
                    "currency" => $request->amount->currency,// "EUR",
                ],
                "times" => $subscriptionLength, // request
                "interval" => $subscriptionLength . " " . $subscriptionMonthOrWeek, // "1 day"
                "description" => "Subscription #{$subscriptionId}",
                "webhookUrl" => $request->webhookUrl . '/api/webhook/MollieSubscriptionPayment',
                "metadata" => [
                    "subscription_id" => $subscriptionId,
                ],
            ]);
                
            $this->entityManager->persist($subscription);
            $this->entityManager->flush();            
        }

        return new JsonResponse(['redirect' => $createPayment->getCheckoutUrl()], 200);
    }
}
