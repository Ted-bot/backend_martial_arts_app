<?php

namespace App\Factory;

use DateTimeImmutable;
use App\Entity\Product;
use App\Factory\CategoryFactory;
use App\Factory\CurrencyTypeFactory;
use App\Repository\ProductRepository;
use App\Factory\SubscriptionTypeFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Product>
 *
 * @method        Product|Proxy                              create(array|callable $attributes = [])
 * @method static Product|Proxy                              createOne(array $attributes = [])
 * @method static Product|Proxy                              find(object|array|mixed $criteria)
 * @method static Product|Proxy                              findOrCreate(array $attributes)
 * @method static Product|Proxy                              first(string $sortedField = 'id')
 * @method static Product|Proxy                              last(string $sortedField = 'id')
 * @method static Product|Proxy                              random(array $attributes = [])
 * @method static Product|Proxy                              randomOrCreate(array $attributes = [])
 * @method static ProductRepository|ProxyRepositoryDecorator repository()
 * @method static Product[]|Proxy[]                          all()
 * @method static Product[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Product[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Product[]|Proxy[]                          findBy(array $attributes)
 * @method static Product[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Product[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class ProductFactory extends PersistentProxyObjectFactory
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
        return Product::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'categoryId' => CategoryFactory::first(),
            'createdAt' => DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'currencyId' => CurrencyTypeFactory::new(),
            'description' => self::faker()->text(510),
            'durationId' => SubscriptionTypeFactory::new(),
            'isPublished' => self::faker()->boolean(),
            'name' => self::faker()->text(100),
            'price' => self::faker()->randomFloat(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Product $product): void {})
        ;
    }
}
