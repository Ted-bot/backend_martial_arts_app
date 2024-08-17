<?php

namespace App\Factory;

use App\Entity\OrderLine;
use App\Repository\OrderLineRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<OrderLine>
 *
 * @method        OrderLine|Proxy                              create(array|callable $attributes = [])
 * @method static OrderLine|Proxy                              createOne(array $attributes = [])
 * @method static OrderLine|Proxy                              find(object|array|mixed $criteria)
 * @method static OrderLine|Proxy                              findOrCreate(array $attributes)
 * @method static OrderLine|Proxy                              first(string $sortedField = 'id')
 * @method static OrderLine|Proxy                              last(string $sortedField = 'id')
 * @method static OrderLine|Proxy                              random(array $attributes = [])
 * @method static OrderLine|Proxy                              randomOrCreate(array $attributes = [])
 * @method static OrderLineRepository|ProxyRepositoryDecorator repository()
 * @method static OrderLine[]|Proxy[]                          all()
 * @method static OrderLine[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static OrderLine[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static OrderLine[]|Proxy[]                          findBy(array $attributes)
 * @method static OrderLine[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static OrderLine[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class OrderLineFactory extends PersistentProxyObjectFactory
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
        return OrderLine::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'price' => self::faker()->randomFloat(),
            'product' => ProductFactory::new(),
            'qty' => self::faker()->randomNumber(),
            'shopOrder' => ShopOrderFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(OrderLine $orderLine): void {})
        ;
    }
}
