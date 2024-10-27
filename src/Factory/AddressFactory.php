<?php

namespace App\Factory;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Address>
 *
 * @method        Address|Proxy                              create(array|callable $attributes = [])
 * @method static Address|Proxy                              createOne(array $attributes = [])
 * @method static Address|Proxy                              find(object|array|mixed $criteria)
 * @method static Address|Proxy                              findOrCreate(array $attributes)
 * @method static Address|Proxy                              first(string $sortedField = 'id')
 * @method static Address|Proxy                              last(string $sortedField = 'id')
 * @method static Address|Proxy                              random(array $attributes = [])
 * @method static Address|Proxy                              randomOrCreate(array $attributes = [])
 * @method static AddressRepository|ProxyRepositoryDecorator repository()
 * @method static Address[]|Proxy[]                          all()
 * @method static Address[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Address[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Address[]|Proxy[]                          findBy(array $attributes)
 * @method static Address[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Address[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class AddressFactory extends PersistentProxyObjectFactory
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
        return Address::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'addressLine' => self::faker()->text(50),
            'city' => self::faker()->text(50),
            'country' => CountryFactory::new(),
            'postalCode' => self::faker()->text(5),
            'streetNumber' => self::faker()->randomNumber(),
            'libReactState' => 2612,
            'libReactCity' => 77340,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Address $address): void {})
        ;
    }
}
