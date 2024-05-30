<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\PostEvent;
use App\Entity\UserProfile;
use App\Factory\AccessTokenFactory;
use App\Factory\PostEventFactory;
use App\Factory\UserFactory;
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
        // $timeSetter = new DateTime($this->time->now()->format('Y-m-d H:i:s'));

        UserFactory::createOne([
            'email' => 'tk@gmail.com',
            'password' => 'sifu020'
        ]);

        UserFactory::createMany(9);

        PostEventFactory::createMany(10);

        AccessTokenFactory::createMany(30, function() {
            return [
                'owendBy' => UserFactory::random(),
            ];
        });
    }
}
