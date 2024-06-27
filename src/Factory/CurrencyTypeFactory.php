<?php

namespace App\Factory;

use App\Entity\CurrencyType;
use App\Repository\CurrencyTypeRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<CurrencyType>
 *
 * @method        CurrencyType|Proxy                              create(array|callable $attributes = [])
 * @method static CurrencyType|Proxy                              createOne(array $attributes = [])
 * @method static CurrencyType|Proxy                              find(object|array|mixed $criteria)
 * @method static CurrencyType|Proxy                              findOrCreate(array $attributes)
 * @method static CurrencyType|Proxy                              first(string $sortedField = 'id')
 * @method static CurrencyType|Proxy                              last(string $sortedField = 'id')
 * @method static CurrencyType|Proxy                              random(array $attributes = [])
 * @method static CurrencyType|Proxy                              randomOrCreate(array $attributes = [])
 * @method static CurrencyTypeRepository|ProxyRepositoryDecorator repository()
 * @method static CurrencyType[]|Proxy[]                          all()
 * @method static CurrencyType[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static CurrencyType[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static CurrencyType[]|Proxy[]                          findBy(array $attributes)
 * @method static CurrencyType[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static CurrencyType[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class CurrencyTypeFactory extends PersistentProxyObjectFactory
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
        return CurrencyType::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(CurrencyType $currencyType): void {})
        ;
    }
}
