<?php

namespace App\Factory;

use App\Entity\VatRate;
use App\Repository\VatRateRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<VatRate>
 *
 * @method        VatRate|Proxy                              create(array|callable $attributes = [])
 * @method static VatRate|Proxy                              createOne(array $attributes = [])
 * @method static VatRate|Proxy                              find(object|array|mixed $criteria)
 * @method static VatRate|Proxy                              findOrCreate(array $attributes)
 * @method static VatRate|Proxy                              first(string $sortedField = 'id')
 * @method static VatRate|Proxy                              last(string $sortedField = 'id')
 * @method static VatRate|Proxy                              random(array $attributes = [])
 * @method static VatRate|Proxy                              randomOrCreate(array $attributes = [])
 * @method static VatRateRepository|ProxyRepositoryDecorator repository()
 * @method static VatRate[]|Proxy[]                          all()
 * @method static VatRate[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static VatRate[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static VatRate[]|Proxy[]                          findBy(array $attributes)
 * @method static VatRate[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static VatRate[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class VatRateFactory extends PersistentProxyObjectFactory
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
        return VatRate::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'procent' => self::faker()->randomFloat(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(VatRate $vatRate): void {})
        ;
    }
}
