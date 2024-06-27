<?php

namespace App\Factory;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Country>
 *
 * @method        Country|Proxy                              create(array|callable $attributes = [])
 * @method static Country|Proxy                              createOne(array $attributes = [])
 * @method static Country|Proxy                              find(object|array|mixed $criteria)
 * @method static Country|Proxy                              findOrCreate(array $attributes)
 * @method static Country|Proxy                              first(string $sortedField = 'id')
 * @method static Country|Proxy                              last(string $sortedField = 'id')
 * @method static Country|Proxy                              random(array $attributes = [])
 * @method static Country|Proxy                              randomOrCreate(array $attributes = [])
 * @method static CountryRepository|ProxyRepositoryDecorator repository()
 * @method static Country[]|Proxy[]                          all()
 * @method static Country[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Country[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Country[]|Proxy[]                          findBy(array $attributes)
 * @method static Country[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Country[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class CountryFactory extends PersistentProxyObjectFactory
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
        return Country::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'code' => self::faker()->text(2),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Country $country): void {})
        ;
    }
}
