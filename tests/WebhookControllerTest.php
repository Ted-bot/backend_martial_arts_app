<?php

namespace App\Tests;

use App\Entity\TokenManager;
use App\Entity\UserProfile;
use App\Service\SubscriptionUUID;
use DateTime;
use App\Class\Role;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\Product;
use App\Entity\VatRate;
use App\Entity\OrderLine;
use App\Entity\ShopOrder;
use App\Entity\ProductVat;
use App\Class\SkuGenerator;
use App\Entity\UserAddress;
use App\Entity\Subscription;
use App\Enum\CountryTypeEnum;
use App\Entity\StatusTransfer;
use App\Enum\CategoryTypeEnum;
use App\Enum\CurrencyTypeEnum;
use Zenstruck\Foundry\Factory;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry AS Foundry;
use App\Enum\SubscriptionTypeEnum;
use App\Enum\MolliePaymentStatusEnum;
use Zenstruck\Foundry\Test\Factories;
use App\Enum\SubscriptionLengthTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\ResetDatabase;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Enum\SubscriptionDirectOrPeriodicTypeEnum;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// class WebhookControllerTest extends ApiTestCase
class WebhookControllerTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    private $entityManager;
    private $client;
    private $userPasswordHasherInterface;

    public function testLogin(): void
    {
        $this->setUp();
        
        $data = [ 
            'json' => ['username' => 'tkbotch@gmail.com', 'password' => 'test_pass']
        ];
        
        $this->client->request(
            'POST',
            '/api/login_check', 
            $data,
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testWebhook(): void
    {
        $this->getSingleUserWithOrder();

        $data = ['json' => ['id' => 'tr_7HYqqTxwki']];

        // $this->client->getProfile();
        $this->client->request(
            'POST', 
            '/api/webhook/MollieDirectPayment',
            $data,
        );
        
        $this->assertEquals('',$this->client->getResponse()->getContent());
        // $this->assertEquals(202,$this->client->getResponse()->getStatusCode());
    }

    public function testSubscriptionWebhook(): void
    {
        $this->getSingleUserWithOrder();
        
        $em = $this->getEntityManager();
        $user = $em->getRepository(User::class)->find(1);

        $subscriptionId = Uuid::fromRfc4122('1ef6c98c-f478-6cfc-a022-b3cca17359bc');
        $subscription = new Subscription();
        $subscription->setStatus(MolliePaymentStatusEnum::OPEN);
        $subscription->setSubscriptionOwnedBy($user);
        $subscription->setUuid($subscriptionId);
        $subscription->setTransferId(null); // webhook also setUpdateAt
        $subscription->setAmount("32.50");
        $subscription->setDuration(SubscriptionLengthTypeEnum::MONTH_FOUR); //SubscriptionLengthTypeEnum
        $subscription->setDateEnd('+' . 3 . ' ' . 'month'); //SubscriptionLengthTypeEnum

        $this->persistAndFlush($subscription);

        $data = ['json' => ['id' => 'tr_7HYqqTxwki']];

        $this->client->request(
            'POST', 
            '/api/webhook/MollieDirectPayment',
            // '/api/webhook/MollieSubscriptionPayment',
            $data,
        );
        
        $this->assertEquals(202, $this->client->getResponse()->getKernelResponse()->getStatusCode());
    }

    #setup functions for tests
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->userPasswordHasherInterface = static::getContainer()->get(UserPasswordHasherInterface::class);
    }
    protected function getEntityManager()
    {
        return $this->entityManager;
    }


    protected function persistAndFlush($class): void
    {
        $this->entityManager->persist($class);
        $this->entityManager->flush();
    }

    protected function getSingleUserWithOrder(): void
    {
        $this->setUp();
        $user = new User();
        // $country = CountryTypeEnum::NL_CODE;
        $shippingAddress = new Address();
        $userAddress = new UserAddress();
        $shopOrder = new ShopOrder();
        $date = new DateTime();
        $product = new Product();
        $statusTransfer = new StatusTransfer();
        $em = $this->getEntityManager();

        $user->setFirstName("Mr.X");
        $user->setLastName("FutureX");
        $user->setPhoneNumber("+31621212121");
        $user->setPassword(
            $this->userPasswordHasherInterface->hashPassword(
                $user, "test_pass"
            )
        );
        $user->setGender("Man");
        $user->setLocation("Amsterdam");
        $user->setEmail("test@gmail.com");
        $user->setDateOfBirth("1990-03-12");
        $user->setConversion("Ik ga de beste worden!");
        $user->setRoles([Role::ROLE_USER_STUDENT]);
        $user->setLibReactState(2612);
        $user->setLibReactCity(77340);
        
        $this->persistAndFlush($user);
        // $this->persistAndFlush($country);

        $shippingAddress->setUnitNumber('-b');
        $shippingAddress->setStreetNumber(5);
        $shippingAddress->setAddressLine('testStreet');
        $shippingAddress->setCity('Amsterdam');
        $shippingAddress->setRegion('North-Holland');
        $shippingAddress->setPostalCode('1111');
        $shippingAddress->setCountry(CountryTypeEnum::NL_CODE);
  
        $this->persistAndFlush($shippingAddress);

        $userAddress->setAddressUser($user);
        $userAddress->setAddress($shippingAddress);
        $userAddress->setDefault(true);

        $this->persistAndFlush($userAddress);
        
        $user = $em->getRepository(User::class)->find($user->getId());
        $shopOrder->setOrderDate($date);
        $shopOrder->setOrderStatus(MolliePaymentStatusEnum::OPEN);
        $shopOrder->setTotalAmount('130.00');        
        $shopOrder->setOrderOwnedBy($user);        
        $shopOrder->setShippingAddress($userAddress);
        $vat = new VatRate();
        $vat->setProcent(9.00);
        
        $prVatRate = new ProductVat();
        $prVatRate->setVatAmount("10.76");
        $prVatRate->setProduct($product);
        $prVatRate->setVatRate($vat);
        
        $product->setName('Group Membership');
        $product->setPrice("130.00");
        $product->setDescription(Foundry\faker()->sentences(1, true));
        $product->setCategory(CategoryTypeEnum::SUB);
        $product->setCurrencyType(CurrencyTypeEnum::EUR);
        $product->setDuration(SubscriptionTypeEnum::MONTH);
        $product->setDirectOrPeriodic(SubscriptionDirectOrPeriodicTypeEnum::DIRECT);
        $product->setRelatedUser($user);
        
        $skuNumber = new SkuGenerator();
        $product->setSku($skuNumber->generateSku("test", "bang", 11));
        $product->setDurationLength(durationLength: SubscriptionLengthTypeEnum::MONTH_FOUR);
        $product->setPublished(isPublished: true);
        
        $line = new OrderLine();
        $line->setPrice($product->getPrice());
        $line->setProduct($product);
        $line->setQty(1);
        $line->setShopOrder($shopOrder);
        
        $this->persistAndFlush($vat);
        $this->persistAndFlush($product);
        $this->persistAndFlush($prVatRate);
        $this->persistAndFlush($shopOrder);
        // $shopOrder->addProduct($product);
        $this->persistAndFlush($line);
        $product->addProductVat($prVatRate);            
        $this->persistAndFlush($product);
        $shopOrder->addOrderLine($line);
        $this->persistAndFlush($shopOrder);
        
        $shopOrder = $em->getRepository(ShopOrder::class)->find($shopOrder->getId());
        $this->persistAndFlush($shopOrder);
        
        $statusTransfer->setTransferId('tr_7HYqqTxwki');
        $statusTransfer->setUserOrder($shopOrder);
        $statusTransfer->setStatus(MolliePaymentStatusEnum::OPEN);
        $statusTransfer->setCustomer('cst_oUiYsKKG3y');

        $this->persistAndFlush($statusTransfer);

        /**
         * @var UserProfile $userProfile
         */
        $userProfile = $this->userProfile($user);
        $this->persistAndFlush($userProfile);        

        $subscription = new Subscription();
        $uuidHelper = new SubscriptionUUID();
        
        
        $timeSub = new DateTime();
        $subscription->setTransferId('tr_7HYqqTxwki');
        // $subscription->setTokenManager($tokenManger);
        $subscription->setUuid($uuidHelper->create());
        $subscription->setAmount('32.50');
        $subscription->setDateStart($timeSub);
        $subscription->setDateEnd($timeSub->modify('+ 5 days')->format('d-m-Y'));
        $subscription->setStatus(MolliePaymentStatusEnum::PAID);
        $subscription->setDuration(SubscriptionLengthTypeEnum::MONTH_FOUR);
        $subscription->setSubscriptionOwnedBy( $user);
        $subscription->setSubscribedProduct($product);
        $this->persistAndFlush($subscription);
        
        $uuidHelper = new SubscriptionUUID();
        // $tokenManger = new TokenManager();
        
        // $tokenManger->setUuid($uuidHelper->create());
        // $tokenManger->setTokens(100);
        // $tokenManger->setRelatedSubscription($subscription);
        // $tokenManger->setUserProfile($userProfile);

        // $this->persistAndFlush($tokenManger);
    }

    public function userProfile($user){
        $userProfile = new UserProfile();
        $userProfile->setDescription('ik ga iedereen slopen');
        $userProfile->setUserName('tedd in ha building');
        $userProfile->setUserUniq($user);

        return $userProfile;

    }
}
