<?php

namespace App\Tests;

use DateTime;
use App\Class\Role;
use App\Entity\User;
use DateTimeInterface;
use App\Entity\Address;
use App\Entity\Country;
use App\Entity\ShopOrder;
use App\Entity\UserAddress;
use App\Entity\StatusTransfer;
use App\Factory\ShopOrderFactory;
use Zenstruck\Foundry\Test\Factories;
use App\Factory\StatusTransferFactory;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\ResetDatabase;
use App\ApiResource\MolliePaymentStatusEnum;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// class WebhookControllerTest extends ApiTestCase
class WebhookControllerTest extends ApiTestCase
{

    use ResetDatabase, Factories;

    // use Refres
    // public function testSomething(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/');

    //     $this->assertResponseIsSuccessful();
    //     $this->assertSelectorTextContains('h1', 'Hello World');
    // }

    private $entityManager;
    private $client;
    private $userPasswordHasherInterface;

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->userPasswordHasherInterface = static::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testLogin(): void
    {
        // $client = static::createClient();
        
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
        $user = new User();

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
        $user->setConversion("Ik ga iedereen slopen let maar op!");
        $user->setRoles([Role::ROLE_USER_STUDENT]);
        $user->setLibReactState(2612);
        $user->setLibReactCity(77340);
        
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
        // $this->entityManager->flush();
        
        $country = new Country('nl', 'nl_NL');

        // $this->entityManager->persist($country);
        $em->persist($country);
        $em->flush();

        $shippingAddress = new Address();

        $shippingAddress->setUnitNumber('-b');
        $shippingAddress->setStreetNumber(5);
        $shippingAddress->setAddressLine('testStreet');
        $shippingAddress->setCity('Amsterdam');
        $shippingAddress->setRegion('North-Holland');
        $shippingAddress->setPostalCode('1111');
        $shippingAddress->setCountry($country);

        $em->persist($shippingAddress);
        $em->flush();       

        $userAddress = new UserAddress();

        // var_dump(['user' => $user->getId()]);
        
        $userAddress->setRelatedUser($user);
        $userAddress->setAddress($shippingAddress);
        $userAddress->setDefault(true);

        $em->persist($userAddress);
        $em->flush();

        $shopOrder = new ShopOrder();
        $date = new DateTime();

        $statusTransfer = new StatusTransfer();
        // dd([
        //     'user' => $user->getId()
        // ]);
        $user = $em->getRepository(User::class)->find($user->getId());

        $shopOrder->setOrderDate($date);
        $shopOrder->setOrderStatus(MolliePaymentStatusEnum::OPEN);
        $shopOrder->setTotalAmount('130.00');        
        $shopOrder->setOwnedBy($user);        
        $shopOrder->setShippingAddress($userAddress);

        $em->persist($shopOrder);
        $em->flush();

        $shopOrder = $em->getRepository(ShopOrder::class)->find($shopOrder->getId());

        // dd([
        //     'shopOrder available?' => $shopOrder,
        //     'shopOrder Id ?' => $shopOrder->getId(),
        // ]);

        // $shopOrder->addOrderline($userAddress);
        $statusTransfer->setTransferId('tr_DBPsz4sq7M');
        // $emShopOrder = $this->entityManager->getRepository(ShopOrder::class)->find($user->getId());
        $statusTransfer->setUserOrder($shopOrder);

        $statusTransfer->setStatus(MolliePaymentStatusEnum::OPEN);

        // dd([
        //     'statusTransfer UserOrder id' => $statusTransfer->getUserOrder(),
        //     'em Shop Order' => $shopOrder->getOwnedBy(),
        // ]);

        // $shopOrder->addStatusTransfer($statusTransfer);
        $em->persist($statusTransfer);
        $em->flush();

        // foreach([$country, $shippingAddress, $userAddress, $shopOrder, $statusTransfer] as $storeData)
        // {
        //     $em->persist($storeData);
        //     $em->flush();
        // }

        // $mollieStatus = MolliePaymentStatusEnum::OPEN;

        // $statusTransfer->create([
        //     'status' => 'paid',
        //     'transfer_id' => 'tr_DBPsz4sq7M',
        // ]);

        // dd($_ENV['APP_ENV']);
        // $client = static::createClient([
        //     'environment' => 'test',
        //     'debug'       => false,
        // ]);
        
        // $data = ['status' => 'paid', 'order_id' => 'test']; // invalid
        $data = ['json' => ['id' => 'tr_DBPsz4sq7M']];
        // $this->browser()
        // ->post('/api/webhook/MollieDirectPayment');

        $this->client->request(
            'POST', 
            '/api/webhook/MollieDirectPayment',
            $data,
        );

        // $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // $backend = static::findIriBy('/webhook/mollie_direct_payment', []);

        // $this->assertIsString($backend);
        // $client->request(
        //     'POST',
        //     '/api/v1/login',
        //     [],
        //     [],
        //     ['HTTP_HOST' => 'localhost:80'],
        //     // $data,
        //     json_encode($data),
        // );
        // $client->request('GET', '/api/products');
        dd(['repsonseTest' => $this->client->getResponse()->getKernelResponse()]);
        // dd(['responseTest' => $client->getResponse()->toArray()]);
        // $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
}
