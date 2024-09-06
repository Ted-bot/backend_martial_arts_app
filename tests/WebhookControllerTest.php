<?php

namespace App\Tests;

use DateTime;
use App\Class\Role;
use App\Entity\User;
use DateTimeInterface;
use App\Entity\Address;
use App\Entity\ShopOrder;
use App\Entity\UserAddress;
use App\Entity\Subscription;
use App\Enum\CountryTypeEnum;
use App\Entity\StatusTransfer;
use Symfony\Component\Uid\Uuid;
use App\Factory\ShopOrderFactory;
use App\Service\SubscriptionUUID;
use App\Enum\MolliePaymentStatusEnum;
use Zenstruck\Foundry\Test\Factories;
use App\Factory\StatusTransferFactory;
use App\Enum\SubscriptionLengthTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\ResetDatabase;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Uuid
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// class WebhookControllerTest extends ApiTestCase
class WebhookControllerTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    private $entityManager;
    private $client;
    private $userPasswordHasherInterface;

    public function testLogin(): void
    {
        // $client = static::createClient();
        $this->setUp();
        
        $data = [ 
            'json' => ['username' => 'tkbotch@gmail.com', 'password' => 'test_pass']
        ];
        
        $this->client->request(
            'POST',
            '/api/login_check', 
            $data,
        );

        // dd(['repsonseTest' => $client->getResponse()->getKernelResponse()]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    public function testWebhook(): void
    {
        $this->getSingleUserWithOrder();

        $data = ['json' => ['id' => 'tr_LSGyD4eoXA']];

        $this->client->request(
            'POST', 
            '/api/webhook/MollieDirectPayment',
            $data,
        );
        
        $this->assertEquals(202, $this->client->getResponse()->getKernelResponse()->getStatusCode());
    }

    public function testSubscriptionWebhook(): void
    {
        $this->getSingleUserWithOrder();

        // $subscriptionId = (new SubscriptionUUID)->create();
        $subscriptionId = Uuid::fromRfc4122('1ef6c98c-f478-6cfc-a022-b3cca17359bc');
        $subscription = new Subscription();
        $subscription->setStatus(MolliePaymentStatusEnum::OPEN);
        // $subscription->setUuid($subscriptionId);
        $subscription->setUuid($subscriptionId);
        $subscription->setTransferId(null); // webhook also setUpdateAt
        $subscription->setAmount("32.50");
        $subscription->setDuration(SubscriptionLengthTypeEnum::MONTH_FOUR); //SubscriptionLengthTypeEnum
        // $currentTime->modify('+' . $setDurationProduct . ' ' . $addMonthOrWeek)->format('Y-m-d')
        $subscription->setDateEnd('+' . 3 . ' ' . 'month'); //SubscriptionLengthTypeEnum

        $this->persistAndFlush($subscription);

        $data = ['json' => ['id' => 'tr_LSGyD4eoXA', 'subscriptionId' => $subscriptionId]];

        $this->client->request(
            'POST', 
            '/api/webhook/MollieSubscriptionPayment',
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

        $userAddress->setRelatedUser($user);
        $userAddress->setAddress($shippingAddress);
        $userAddress->setDefault(true);

        $this->persistAndFlush($userAddress);
        
        $user = $em->getRepository(User::class)->find($user->getId());
        $shopOrder->setOrderDate($date);
        $shopOrder->setOrderStatus(MolliePaymentStatusEnum::OPEN);
        $shopOrder->setTotalAmount('130.00');        
        $shopOrder->setOwnedBy($user);        
        $shopOrder->setShippingAddress($userAddress);

        $this->persistAndFlush($shopOrder);

        $shopOrder = $em->getRepository(ShopOrder::class)->find($shopOrder->getId());
        
        $statusTransfer->setTransferId('tr_LSGyD4eoXA');
        $statusTransfer->setUserOrder($shopOrder);
        $statusTransfer->setStatus(MolliePaymentStatusEnum::OPEN);
        $statusTransfer->setCustomer('cst_HWJKkmZeqA');

        $this->persistAndFlush($statusTransfer);
    }
}
