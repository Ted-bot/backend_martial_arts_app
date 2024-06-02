<?php

namespace App\Factory;

use App\Entity\UserProfile;
use App\Repository\UserProfileRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<UserProfile>
 *
 * @method        UserProfile|Proxy                     create(array|callable $attributes = [])
 * @method static UserProfile|Proxy                     createOne(array $attributes = [])
 * @method static UserProfile|Proxy                     find(object|array|mixed $criteria)
 * @method static UserProfile|Proxy                     findOrCreate(array $attributes)
 * @method static UserProfile|Proxy                     first(string $sortedField = 'id')
 * @method static UserProfile|Proxy                     last(string $sortedField = 'id')
 * @method static UserProfile|Proxy                     random(array $attributes = [])
 * @method static UserProfile|Proxy                     randomOrCreate(array $attributes = [])
 * @method static UserProfileRepository|RepositoryProxy repository()
 * @method static UserProfile[]|Proxy[]                 all()
 * @method static UserProfile[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static UserProfile[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static UserProfile[]|Proxy[]                 findBy(array $attributes)
 * @method static UserProfile[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static UserProfile[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class UserProfileFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'username' => self::faker()->userName(),
            'description' => self::faker()->sentences(2, true),
            'websiteUrl' => self::faker()->url(),
            'group_student' => null,
            'userUniq' => UserFactory::random()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(UserProfile $userProfile): void {})
        ;
    }

    protected static function getClass(): string
    {
        return UserProfile::class;
    }
}
