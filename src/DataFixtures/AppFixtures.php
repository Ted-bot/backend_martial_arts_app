<?php

namespace App\DataFixtures;

use DateTime;
use DateTimeImmutable;
use Carbon\Carbon;
use App\Class\Roles;
use App\Entity\User;
use App\Entity\PostEvent;
use App\Entity\UserProfile;
use App\Factory\UserFactory;
use App\Factory\GroupFactory;
use Zenstruck\Foundry\Factory;
use App\Factory\PostEventFactory;
use App\Factory\UserProfileFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Clock\ClockInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        protected ClockInterface $time
    ) {}

    public function load(ObjectManager $manager): void
    {        
        UserFactory::createSequence(
            function() {
                foreach (range(1, 10) as $i) {
                    // yield [new UserFactory()];
                    yield [
                        'firstName' => Factory::faker()->firstName(),
                        'lastName' => Factory::faker()->lastName(),
                        'email' => Factory::faker()->unique()->email(),
                        'phoneNumber' => substr(Factory::faker()->phoneNumber(), 1, 15),
                        'dateOfBirth' => Carbon::parse(Factory::faker()->dateTimeBetween('-30 years', '-8 years'))->format('d-m-Y'),
                        'gender' => Factory::faker()->text(6),
                        'location' => Factory::faker()->city(),
                        'password' => Factory::faker()->password(),
                        'conversion' => Factory::faker()->sentences(2, true),
                    ];
                }
            }
        );

        UserProfileFactory::createSequence(
            function() {
                foreach (UserFactory::all() as $user) {
                    // yield [new UserFactory()];
                    yield [
                        'userUniq' => $user
                    ];
                }
            }
        );

        PostEventFactory::createSequence(
            function() {
                foreach (range(1, 10) as $i) {
                    yield [
                        'title' => Factory::faker()->title(),
                        'description' => Factory::faker()->sentences(2, true),
                        'relatedUser' => UserProfileFactory::first(),
                        'createdAt' => Factory::faker()->dateTimeBetween('-1 years','now'),
                        'eventDate' => date_create(Factory::faker()->date()),
                        'eventStart' => Factory::faker()->datetime(),
                        'eventEnd' => Factory::faker()->datetime(),
                        'eventRegular' => true
                    ];
                }
            }
        );
    }
}
