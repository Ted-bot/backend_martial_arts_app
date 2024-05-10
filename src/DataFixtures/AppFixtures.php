<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\PostEvent;
use App\Entity\UserProfile;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Clock\ClockInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        protected ClockInterface $time
    ) {}

    public function load(ObjectManager $manager): void
    {
        $timeSetter = new DateTime($this->time->now()->format('Y-m-d H:i:s'));

        $user = new User();
        $user->setEmail('john@email.com');
        $user->setFirstName('John');
        $user->setLastName('van den heuvel');
        $user->setConversion('Ik wilde graag trainen :p');
        $user->setGender('man');
        $user->setLocation('Amsterdam');
        $user->setPhoneNumber(123456789);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                '12345'
            )
        );        
        
        $userProf = new UserProfile();
        $userProf->setName('John');
        $userProf->setUserUniq($user);
        $userProf->setCreated($timeSetter);
        $userProf->setConversion($user->getConversion());
        
        $user2 = new User();
        $user2->setEmail('Ellen@elvismail.com');
        $user2->setFirstName('Ellen');
        $user2->setLastName('Groenberg');
        $user2->setConversion('Ik wilde graag vechten :D');
        $user2->setGender('vrouw');
        $user2->setLocation('Zaandam');
        $user2->setPhoneNumber(123456789);
        $user2->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                '12345'
            )
        );
        
        $userProf2 = new UserProfile();
        $userProf2->setName('Elvis');
        $userProf2->setUserUniq($user2);
        $userProf2->setConversion($user2->getConversion());
        $userProf2->setCreated($timeSetter);

        $postEvent = new PostEvent();
        $postEvent->setTitle('Testing');
        $postEvent->setDescription('Hi there, Next class we will be...');
        $postEvent->setCreated($timeSetter);

        $postEvent_2 = new PostEvent();
        $postEvent_2->setTitle('Testing');
        $postEvent_2->setDescription('Hi there, This class we will be...');
        $postEvent_2->setCreated($timeSetter);
        
        $manager->persist($user);
        $manager->persist($userProf);
        $manager->persist($user2);
        $manager->persist($userProf2);
        $manager->persist($postEvent);
        $manager->persist($postEvent_2);

        $manager->flush();
    }
}
