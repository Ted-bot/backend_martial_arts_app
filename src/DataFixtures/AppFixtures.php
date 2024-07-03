<?php

namespace App\DataFixtures;

use Brick\Math\BigDecimal;
use DateInterval;
// use Doctrine\DBAL\Types\DecimalType
use Brick\Math\BigInteger;
use Brick\Math\RoundingMode;
use Carbon\Carbon;
use App\Class\Role;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\Country;
use App\Entity\Product;
use App\Entity\VatRate;
use App\Entity\Category;
use App\Entity\OrderLine;
use App\Entity\ShopOrder;
use App\Entity\ProductVat;
use App\Entity\OrderStatus;
use App\Entity\UserAddress;
use App\Entity\CurrencyType;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Factory;
use App\Factory\AddressFactory;
use App\Factory\CountryFactory;
use App\Factory\ProductFactory;
use App\Factory\VatRateFactory;
use App\Entity\SubscriptionType;
use App\Factory\CategoryFactory;
use App\Factory\PostEventFactory;
use App\Repository\UserRepository;
use App\Factory\UserAddressFactory;
use App\Factory\UserProfileFactory;

use App\Factory\CurrencyTypeFactory;
use App\Entity\ProductVatRateFactory;
use App\Repository\AddressRepository;
use App\Repository\CountryRepository;
use App\Repository\ProductRepository;
use App\Repository\VatRateRepository;
use App\Repository\CategoryRepository;
use App\Repository\PostEventRepository;
use Doctrine\Persistence\ObjectManager;
use App\Factory\SubscriptionTypeFactory;
use App\Repository\ProductVatRepository;
use App\Repository\OrderStatusRepository;
use App\Repository\UserAddressRepository;

use App\Repository\UserProfileRepository;
use App\Repository\CurrencyTypeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Clock\ClockInterface;
use App\Repository\SubscriptionTypeRepository;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        protected ClockInterface $time,
        protected UserPasswordHasherInterface $userPasswordHasherInterface,
        protected UserRepository $userRepository,
        protected UserProfileRepository $userProfileRepository,
        protected PostEventRepository $postEvent,
        protected SubscriptionTypeRepository $subscriptionTypeRepository,
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository,
        protected CurrencyTypeRepository $currencyTypeRepository,
        protected User $user,
        protected ProductVat $prVat,
        protected VatRateRepository $vatRepo,
        protected ProductVatRepository $prVatRepo,
        protected CountryRepository $countryRepo,
        protected AddressRepository $addressRepo,
        protected OrderStatusRepository $orderStatusRepo,
        protected UserAddressRepository $userAddressRepo,
    ) {
        // $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {        
        $allUsers = [];

        $user = new User();
        $user->setEmail("tkbotch@gmail.com");
        //$user->setPassword("test_pass");
        $user->setPassword(
            $this->userPasswordHasherInterface->hashPassword(
                $user, "test_pass"
            )
        );

        $user->setFirstName("Mr.X");
        $user->setLastName("FutureX");
        $user->setPhoneNumber("0621212121");
        $user->setGender("Man");
        $user->setLocation("Amsterdam");
        $user->setDateOfBirth("12-03-1990");
        $user->setConversion("Ik ga iedereen slopen let maar op!");
        $user->setRoles([Role::ROLE_USER_STUDENT]);

        $manager->persist($user);
       
        foreach (range(1, 20) as $i) {
            $user = new User();
            $user->setEmail(Factory::faker()->unique()->email());
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $user, "test_pass"
                )
            );
            $gender = Factory::faker()->boolean() ? 'man' : 'woman';
            $user->setFirstName(Factory::faker()->firstName());
            $user->setLastName(Factory::faker()->lastName());
            $user->setPhoneNumber(substr(Factory::faker()->phoneNumber(), 1, 15));
            $user->setGender($gender);
            $user->setLocation(Factory::faker()->city());
            $user->setDateOfBirth(Carbon::parse(Factory::faker()->dateTimeBetween('-30 years', '-8 years'))->format('d-m-Y'));
            $user->setConversion(Factory::faker()->sentences(2, true));

            $allUsers[$i] = $user;
            $manager->persist($user);
        }

        $manager->flush();

        UserProfileFactory::createSequence(
            function() use ($allUsers)  {
                foreach ($allUsers as $user) {

                    yield [
                        'userUniq' => $user
                    ];
                }
            }
        );

        PostEventFactory::createSequence(
            function() {
                foreach (range(0, 50) as $i) {
                    $startDate = Carbon::createFromTimeStamp(Factory::faker()->dateTimeBetween('-10 days', '+30 days')->getTimestamp());
                    $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)->addHour();
                    $titles = ['training', 'expeditie', 'training', 'special', 'training'];
                    
                    yield [
                        'title' => $titles[array_rand($titles)],
                        'description' => Factory::faker()->sentences(2, true),
                        'relatedUser' => UserProfileFactory::first(),
                        'createdAt' => Factory::faker()->dateTimeBetween('-1 month','now'),
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                        'allDay' => Factory::faker()->boolean()
                    ];
                }
            }
        );

        $postAll = $this->postEvent->findAll();
        foreach($postAll as $trainingSession){
            foreach($this->userProfileRepository->findAll() as $userProfile) {
                if(Factory::faker()->boolean()){                        
                    $trainingSession->addSubscribe($userProfile);

                    $manager->persist($trainingSession);
                }
            }
        }

        $category = new Category();
        $currency = new CurrencyType();
        
        $category->setName('subscription');
        $currency->setName('euro');

        $manager->persist($category);
        $manager->persist($currency);

        $typesSubscription = ['one_week','one_month','no_duration'];
        
        foreach($typesSubscription as $i) {
            $subscriptionType = new SubscriptionType();
            $subscriptionType->setDuration($i);
            $manager->persist($subscriptionType);
        }

        $vat = new VatRate();
        $vat->setProcent(9.00);

        $manager->persist($vat);

        $manager->flush();
        
        $subscriptionNames = ['try_out_once','try_out_five','full_month'];
        $productPrices = [80,150,250];
        foreach ($this->subscriptionTypeRepository->findAll() as $key => $subscriptionType) {
            $prVatRate = new ProductVat();
            $product = new Product();
            $product->setName($subscriptionNames[$key]);
            $product->setPrice($productPrices[$key]);
            $product->setDescription(Factory::faker()->sentences(2, true));
            // $product->setCreatedAt(Factory::faker()->dateTimeBetween('-1 month','now'));
            $product->setCategory($this->categoryRepository->findOneBy(['name'=> 'subscription']));
            $product->setCurrencyType($this->currencyTypeRepository->findOneBy(['name'=> 'euro']));
            $product->setDuration($this->subscriptionTypeRepository->find($subscriptionType->getId()));
            $product->setRelatedUser($this->userRepository->findOneBy(['email'=> 'tkbotch@gmail.com']));

            $setVatRate = $this->vatRepo->findOneBy(['procent' => 9.00]);
            $tax = BigDecimal::ofUnscaledValue($productPrices[$key])
            ->dividedBy(100, 2, RoundingMode::UP)
            ->multipliedBy($setVatRate->getProcent());
            // $tax = (($productPrices[$key] / 100) * $setVatRate->getProcent() );

            $prVatRate->setVatAmount($tax);
            $prVatRate->setProduct($product);
            $prVatRate->setVatRate($setVatRate);

            $manager->persist($prVatRate);
            $manager->persist($product);
        } 
        
        $country = new Country();
        $country->setCode('NL');
        $manager->persist($country);
        
        $manager->flush();

        $arrayStatusses= ['saved','in_process','paid'];
        foreach($arrayStatusses as $status) {
            $orderStatus = new OrderStatus();
            $orderStatus->setStatus($status);

            $manager->persist($orderStatus);
        }

        foreach(range(0,19) as $i) {
            $address = new Address();
            $address->setCity('Amsterdam');
            $address->setCountry($this->countryRepo->findOneBy(['code' => 'NL']));
            $address->setPostalCode(Factory::faker()->postcode());
            $address->setAddressLine(Factory::faker()->address());
            $address->setStreetNumber(Factory::faker()->numberBetween(0, 5000));
            
            $manager->persist($address);            
        }

        $manager->flush();

        // Create addresses for Users
        foreach($this->userRepository->findAll() as $key => $user){

            $userAddress = new UserAddress();
            $randomAddress = AddressFactory::random();
            $address = $this->addressRepo->findOneBy(['id' => $randomAddress->getId()]);

            $userAddress->setRelatedUser($user);
            $userAddress->setAddress($address);
            $userAddress->setDefault(true);

            $manager->persist($userAddress);

        }

        foreach($this->userRepository->findAll() as $key => $user){

            $userAddress = new UserAddress();
            $randomAddress = AddressFactory::random();
            $address = $this->addressRepo->findOneBy(['id' => $randomAddress->getId()]);

            $userAddress->setRelatedUser($user);
            $userAddress->setAddress($address);
            $userAddress->setDefault(false);

            $manager->persist($userAddress);
        }

        $manager->flush();

        // Create Shop Orders
        foreach($this->userRepository->findAll() as $key => $user){
            
            $shopOrder = new ShopOrder();
            $productNames = ['full_month', 'try_out_five', 'try_out_once'];
            
            // if necessary loop multiProducts
            foreach($productNames as $name) {
                $orderLine = new OrderLine();
                $product = $this->productRepository->findOneBy(['name'=> $name]);

                $productTax = $this->prVatRepo->findOneBy(['product'=> $product->getId()]);
                $statusOrder = $this->orderStatusRepo->findOneBy(['status' => 'saved']);
                $shippingAddress = $this->userAddressRepo->findOneBy(['relatedUser'=> $user->getId(), 'isDefault' => true ]);

                $quantity = [1,2,3];
                $orderLine->setQty($quantity[array_rand($quantity)]);
                $totalTaxQtyProducts = $productTax->getVatAmount() * $orderLine->getQty();
                $totalProductPriceWithQty = $product->getPrice() * $orderLine->getQty();
                $totalAmountOrder = $totalProductPriceWithQty + $totalTaxQtyProducts;
                
                $orderLine->setShopOrder($shopOrder); // maakt niewe order
                $orderLine->setPrice($totalProductPriceWithQty);
                $orderLine->setProduct($product);
                
                $shopOrder->setOwnedBy($user);
                $shopOrder->setTotalAmount($totalAmountOrder);  // includes tax, Qty of product, ?shippingPrice
                $shopOrder->setOrderDate(Factory::faker()->dateTimeBetween('-2 month','now'));
                $shopOrder->setOrderStatus($statusOrder);
                $shopOrder->setShippingAddress($shippingAddress);
                
                $manager->persist($shopOrder);
                $manager->persist($orderLine);
            }
            $manager->flush();
            // end loop for products
        }

        $manager->flush();        
    }
}
