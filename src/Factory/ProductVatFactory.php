<?php

namespace App\Factory;

use App\Entity\ProductVat;
use App\Repository\ProductVatRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<ProductVat>
 *
 * @method        ProductVat|Proxy                              create(array|callable $attributes = [])
 * @method static ProductVat|Proxy                              createOne(array $attributes = [])
 * @method static ProductVat|Proxy                              find(object|array|mixed $criteria)
 * @method static ProductVat|Proxy                              findOrCreate(array $attributes)
 * @method static ProductVat|Proxy                              first(string $sortedField = 'id')
 * @method static ProductVat|Proxy                              last(string $sortedField = 'id')
 * @method static ProductVat|Proxy                              random(array $attributes = [])
 * @method static ProductVat|Proxy                              randomOrCreate(array $attributes = [])
 * @method static ProductVatRepository|ProxyRepositoryDecorator repository()
 * @method static ProductVat[]|Proxy[]                          all()
 * @method static ProductVat[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static ProductVat[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static ProductVat[]|Proxy[]                          findBy(array $attributes)
 * @method static ProductVat[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static ProductVat[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class ProductVatFactory extends PersistentProxyObjectFactory
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
        return ProductVat::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'VatAmount' => self::faker()->randomFloat(),
            'product' => ProductFactory::new(),
            'vatRate' => VatRateFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ProductVat $productVat): void {})
        ;
    }
}
