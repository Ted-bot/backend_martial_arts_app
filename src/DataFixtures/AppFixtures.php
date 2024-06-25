<?php

namespace App\DataFixtures;

use DateInterval;
use Carbon\Carbon;
use App\Class\Role;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\CurrencyType;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Factory;
use App\Factory\ProductFactory;
use App\Entity\SubscriptionType;
use App\Factory\CategoryFactory;
use App\Factory\PostEventFactory;
use App\Repository\UserRepository;
use App\Factory\UserProfileFactory;
use App\Factory\CurrencyTypeFactory;
use App\Repository\CategoryRepository;
use App\Repository\PostEventRepository;

use Doctrine\Persistence\ObjectManager;
use App\Factory\SubscriptionTypeFactory;
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
        protected CurrencyTypeRepository $currencyTypeRepository,
        protected User $user,
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
                foreach (range(0, 14) as $i) {
                    $startDate = Carbon::createFromTimeStamp(Factory::faker()->dateTimeBetween('-1 days', '+30 days')->getTimestamp());
                    $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)->addHour();
                    yield [
                        'title' => Factory::faker()->title(),
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
        
        $category->setName('subscription');

        $manager->persist($category);

        $currency = new CurrencyType();

        $currency->setName('euro');

        $manager->persist($currency);

        $typesSubscription = ['one_week','one_month','no_duration'];
        
        foreach($typesSubscription as $i) {
            $subscriptionType = new SubscriptionType();
            $subscriptionType->setDuration($i);
            $manager->persist($subscriptionType);
        }
        
        $manager->flush();

        $subscriptionNames = ['try_out_once','try_out_five','full_month'];
        $productPrices = [8000,15000,25000];
        
        foreach ($this->subscriptionTypeRepository->findAll() as $key => $subscriptionType) {
            $product = new Product();
            $product->setName($subscriptionNames[$key]);
            $product->setPrice($productPrices[$key]);
            $product->setDescription(Factory::faker()->sentences(2, true));
            // $product->setCreatedAt(Factory::faker()->dateTimeBetween('-1 month','now'));
            $product->setCategory($this->categoryRepository->findOneBy(['name'=> 'subscription']));
            $product->setCurrencyType($this->currencyTypeRepository->findOneBy(['name'=> 'euro']));
            $product->setDuration($this->subscriptionTypeRepository->find($subscriptionType->getId()));
            $product->setRelatedUser($this->userRepository->findOneBy(['email'=> 'tkbotch@gmail.com']));

            $manager->persist($product);
        }

        $manager->flush();


        
    }
}
