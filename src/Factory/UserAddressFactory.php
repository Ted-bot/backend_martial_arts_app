<?php

namespace App\Factory;

use App\Entity\UserAddress;
use App\Repository\UserAddressRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<UserAddress>
 *
 * @method        UserAddress|Proxy                              create(array|callable $attributes = [])
 * @method static UserAddress|Proxy                              createOne(array $attributes = [])
 * @method static UserAddress|Proxy                              find(object|array|mixed $criteria)
 * @method static UserAddress|Proxy                              findOrCreate(array $attributes)
 * @method static UserAddress|Proxy                              first(string $sortedField = 'id')
 * @method static UserAddress|Proxy                              last(string $sortedField = 'id')
 * @method static UserAddress|Proxy                              random(array $attributes = [])
 * @method static UserAddress|Proxy                              randomOrCreate(array $attributes = [])
 * @method static UserAddressRepository|ProxyRepositoryDecorator repository()
 * @method static UserAddress[]|Proxy[]                          all()
 * @method static UserAddress[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static UserAddress[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static UserAddress[]|Proxy[]                          findBy(array $attributes)
 * @method static UserAddress[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static UserAddress[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class UserAddressFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return UserAddress::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'address' => AddressFactory::new(),
            'isDefault' => self::faker()->boolean(),
            'relatedUser' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(UserAddress $userAddress): void {})
        ;
    }
}
