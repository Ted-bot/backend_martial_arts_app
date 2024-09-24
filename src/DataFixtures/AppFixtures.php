<?php

namespace App\DataFixtures;

use Carbon\Carbon;
use App\Class\Role;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\Country;
use App\Entity\Product;
use App\Entity\VatRate;
use App\Entity\OrderLine;
use App\Entity\ShopOrder;
use App\Entity\ProductVat;
// use App\Entity\Category;
use Brick\Math\BigDecimal;
use App\Class\SkuGenerator;
use App\Entity\UserAddress;
use App\Entity\CurrencyType;
use App\Factory\UserFactory;
use Brick\Math\RoundingMode;
use App\Enum\CountryTypeEnum;
use App\Entity\StatusTransfer;
use App\Enum\CategoryTypeEnum;
use App\Enum\CurrencyTypeEnum;
use Zenstruck\Foundry\Factory;
use App\Factory\AddressFactory;
use App\Factory\CountryFactory;
use App\Factory\ProductFactory;
use App\Factory\VatRateFactory;
use App\Entity\SubscriptionType;
use App\Factory\CategoryFactory;
use App\Factory\PostEventFactory;
use App\Enum\SubscriptionTypeEnum;
use App\Repository\UserRepository;
use App\Factory\UserAddressFactory;
use App\Factory\UserProfileFactory;
use App\Factory\CurrencyTypeFactory;
use App\Entity\ProductVatRateFactory;
use App\Enum\MolliePaymentStatusEnum;
use App\Repository\AddressRepository;
use App\Repository\CountryRepository;
use App\Repository\ProductRepository;
use App\Repository\VatRateRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductsRepository;
use App\Repository\PostEventRepository;
use App\Repository\ShopOrderRepository;
use Doctrine\Persistence\ObjectManager;
use App\Enum\SubscriptionLengthTypeEnum;
use App\Factory\SubscriptionTypeFactory;

use App\Repository\ProductVatRepository;
use App\Repository\UserAddressRepository;
use App\Repository\UserProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Clock\ClockInterface;

use App\Repository\StatusTransferRepository;
use App\Enum\SubscriptionDirectOrPeriodicTypeEnum;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private StatusTransferRepository $stRepo,
        private UserPasswordHasherInterface $userPasswordHasher,
        private ClockInterface $time,
        private UserPasswordHasherInterface $userPasswordHasherInterface,
        private UserRepository $userRepository,
        private UserProfileRepository $userProfileRepository,
        private PostEventRepository $postEvent,
        private ProductRepository $productRepo,
        private ShopOrderRepository $soRepo,
        private User $user,
        private ProductVat $prVat,
        private VatRateRepository $vatRepo,
        private ProductVatRepository $prVatRepo,
        private AddressRepository $addressRepo,
        private UserAddressRepository $userAddressRepo,
        private ManagerRegistry $em
    ) {
        // $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {        
        $allUsers = [];

        $user = new User();
        $user->setEmail("tkbotch@gmail.com");
        $user->setPassword(
            $this->userPasswordHasherInterface->hashPassword(
                $user, "test_pass"
            )
        );

        $user->setFirstName("Mr.X");
        $user->setLastName("FutureX");
        $user->setPhoneNumber("+31621212121");
        $user->setGender("Man");
        $user->setLocation("Amsterdam");
        $user->setDateOfBirth("1990-03-12");
        $user->setConversion("Ik ga iedereen slopen let maar op!");
        $user->setRoles([Role::ROLE_USER_STUDENT]);
        $user->setLibReactState(2612);
        $user->setLibReactCity(77340);

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
            $user->setDateOfBirth(Carbon::parse(Factory::faker()->dateTimeBetween('-30 years', '-8 years'))->format('Y-m-d'));
            $user->setConversion(Factory::faker()->sentences(2, true));
            $user->setLibReactState(2612);
            $user->setLibReactCity(77340);

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

        // $categoryTypes = CategoryTypeEnum::getCases();
        // $typesSubscription = SubscriptionTypeEnum::getCases();
        $vat = new VatRate();

        $vat->setProcent(9.00);
        $manager->persist($vat);

        $manager->flush();
        
        $latestPr = 0;
        $productPrices = [1,130,1];
        $productNames = ['Group Trail: 2 Lessons', 'Group MemberShip', 'BD MA T-Shirt'];

        // $newProduct = new Product();
        // $prVatRate = new ProductVat();

        // $newProduct->setName('Group: Subscribe 4 month');
        // $newProduct->setPrice($productPrices[1]);
        // $newProduct->setDescription(Factory::faker()->sentences(1, true));
        // $newProduct->setCategory(CategoryTypeEnum::SUB);
        // $newProduct->setCurrencyType(CurrencyTypeEnum::EUR);
        // $newProduct->setDuration(SubscriptionTypeEnum::MONTH);
        // $newProduct->setDurationLength(SubscriptionLengthTypeEnum::MONTH_FOUR);
        // $newProduct->setDirectOrPeriodic(SubscriptionDirectOrPeriodicTypeEnum::PERIODIC);
        // $newProduct->setRelatedUser($this->userRepository->findOneBy(['email'=> 'tkbotch@gmail.com']));
            
        // $skuNumber = new SkuGenerator();
        // $new_skuStart = $newProduct->getCategory()->getId();
        // $new_skuMid = $newProduct->getDuration()->getId();
        // $new_skuEnd = 99;
        
        // $newProduct->setSku($skuNumber->generateSku($new_skuStart, $new_skuMid, $new_skuEnd));

        // $setVatRate = $this->vatRepo->findOneBy(['procent' => 9.00]);
        // $productTotalProcentPlusProcent = 100 + $setVatRate->getProcent();
        // $divideProcentByTotalProductProcent = BigDecimal::of($setVatRate->getProcent())
        // ->dividedBy($productTotalProcentPlusProcent, 4,  RoundingMode::DOWN);

        // $tax = BigDecimal::ofUnscaledValue($productPrices[1])
        // ->multipliedBy($divideProcentByTotalProductProcent);

        // $prVatRate->setVatAmount($tax);
        // $prVatRate->setProduct($newProduct);
        // $prVatRate->setVatRate($setVatRate);

        // $manager->persist($prVatRate);
        // $manager->persist($newProduct);
        // $manager->flush();

        foreach (SubscriptionTypeEnum::getCases() as $key => $subscriptionType) {

            $prVatRate = new ProductVat();
            $product = new Product();
            $latestPr++;

            $product->setName($productNames[$key]);
            $product->setPrice($productPrices[$key]);
            $product->setDescription(Factory::faker()->sentences(1, true));
            $parseString = explode(" ",$productNames[$key]);

            $compareString = strcmp($parseString[0], 'Group');
            $compareSecondString = strcmp($parseString[1], 'Membership');

            $setCategoryDecider = $compareString !== 0;
            $setCategorySecondDecider = $compareSecondString !== 0;

            if($setCategoryDecider == false &&  $setCategorySecondDecider == false)
            {
                $setCategoryType = 'subscription';
            } elseif ($setCategoryDecider == false &&  $setCategorySecondDecider == true){
                $setCategoryType = 'subscription';
            } else {
                $setCategoryType = 'heren';
            }           

            $catType = CategoryTypeEnum::tryFrom($setCategoryType);
            $product->setCategory($catType);
            $product->setCurrencyType(CurrencyTypeEnum::EUR);
            $product->setDuration($subscriptionType);
            $subscriptionType == SubscriptionTypeEnum::MONTH ? $product->setDurationLength(SubscriptionLengthTypeEnum::MONTH_FOUR)
            : ($subscriptionType != SubscriptionTypeEnum::WEEK 
                ? $product->setDurationLength(SubscriptionLengthTypeEnum::UNAVAILABLE)
                : $product->setDurationLength(SubscriptionLengthTypeEnum::PERIOD_TIMES_TWO)
            );
            $product->setDirectOrPeriodic(SubscriptionDirectOrPeriodicTypeEnum::DIRECT);
            $product->setRelatedUser($this->userRepository->findOneBy(['email'=> 'tkbotch@gmail.com']));
            
            // $skuNumber = new SkuGenerator($this->em);
            $skuNumber = new SkuGenerator();
            // $subscriptionType = $this->subscriptionTypeRepository->findOneBy(['id' => $product->getDuration()]);
            $skuStart = $product->getCategory()->getId();
            $skuMid = $product->getDuration()->getId();

            $skuEnd = $latestPr;

            $product->setSku($skuNumber->generateSku($skuStart, $skuMid, $skuEnd));

            $setVatRate = $this->vatRepo->findOneBy(['procent' => 9.00]);
            $productTotalProcentPlusProcent = 100 + $setVatRate->getProcent();
            $divideProcentByTotalProductProcent = BigDecimal::of($setVatRate->getProcent())
            ->dividedBy($productTotalProcentPlusProcent, 4,  RoundingMode::DOWN);

            $tax = BigDecimal::ofUnscaledValue($productPrices[$key])
            ->multipliedBy($divideProcentByTotalProductProcent);

            $prVatRate->setVatAmount($tax);
            $prVatRate->setProduct($product);
            $prVatRate->setVatRate($setVatRate);

            
            $manager->persist($prVatRate);
            $manager->persist($product);
        } 
        
        // $country = new Country('NL', 'nl_NL');
        // $country = CountryTypeEnum::;
        // $manager->persist($country);        
        $manager->flush();
        // dd(['product' => $product]);
        
        foreach(range(0,19) as $i) {
            $address = new Address();
            $address->setCity('Amsterdam');
            $address->setCountry(CountryTypeEnum::NL_CODE);
            $address->setPostalCode(substr(Factory::faker()->postcode(), 0, 4));
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
            
            $totalAmountOrder = BigDecimal::ofUnscaledValue(0);
            $shopOrder = new ShopOrder();
            
            // if necessary loop multiProducts
            foreach($productNames as $name) {

                if($name === 'BD MA T-Shirt') {continue;}
                if($name === 'Group Trail: 2 Lessons') {continue;}

                $orderLine = new OrderLine();
                $product = $this->productRepo->findOneBy(['name'=> $name]);

                // $productTax = $this->prVatRepo->findOneBy(['product'=> $product->getId()]);
                $shippingAddress = $this->userAddressRepo->findOneBy(['relatedUser'=> $user->getId(), 'isDefault' => true ]);

                // $quantity = [1,2,3];
                // $orderLine->setQty($quantity[array_rand($quantity)]);
                $orderLine->setQty(1);
                // $totalTaxQtyProducts = $productTax->getVatAmount() * $orderLine->getQty();
                $totalProductPriceWithQty = $product->getPrice() * $orderLine->getQty();
                // $totalAmountOrderInclTax = BigDecimal::ofUnscaledValue($totalProductPriceWithQty)->plus($totalTaxQtyProducts);
                $totalAmountOrderExclTax = BigDecimal::ofUnscaledValue($totalProductPriceWithQty);
                
                $totalAmountOrder = $totalAmountOrder->plus($totalAmountOrderExclTax);

                $orderLine->setShopOrder($shopOrder); // maakt niewe order
                $orderLine->setPrice($totalProductPriceWithQty);
                $orderLine->setProduct($product);
                
                $shopOrder->setOrderOwnedBy($user);
                $shopOrder->setTotalAmount($totalAmountOrder);  // includes tax, Qty of product, ?shippingPrice
                $shopOrder->setOrderDate(Factory::faker()->dateTimeBetween('-2 month','now'));
                $shopOrder->setShippingAddress($shippingAddress);
                
                $manager->persist($shopOrder);
                $manager->persist($orderLine);
            }

            $manager->flush();
            // end loop for products
        }

        $manager->flush();        

        // create fake orderPayment
        $paymentUpdate = new StatusTransfer();
        $userOrder = $this->soRepo->findOneBy(['orderOwnedBy' =>  $user->getId()]);
        $paymentUpdate->setTransferId('tr_DBPsz4sq7M');
        $paymentUpdate->setUserOrder($userOrder);

        $manager->persist($paymentUpdate);   
        $manager->flush();   

    }

}
