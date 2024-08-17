<?php

namespace App\Factory;

use App\ApiResource\MolliePaymentStatusEnum;
use App\Entity\ShopOrder;
use App\Repository\ShopOrderRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<ShopOrder>
 *
 * @method        ShopOrder|Proxy                              create(array|callable $attributes = [])
 * @method static ShopOrder|Proxy                              createOne(array $attributes = [])
 * @method static ShopOrder|Proxy                              find(object|array|mixed $criteria)
 * @method static ShopOrder|Proxy                              findOrCreate(array $attributes)
 * @method static ShopOrder|Proxy                              first(string $sortedField = 'id')
 * @method static ShopOrder|Proxy                              last(string $sortedField = 'id')
 * @method static ShopOrder|Proxy                              random(array $attributes = [])
 * @method static ShopOrder|Proxy                              randomOrCreate(array $attributes = [])
 * @method static ShopOrderRepository|ProxyRepositoryDecorator repository()
 * @method static ShopOrder[]|Proxy[]                          all()
 * @method static ShopOrder[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static ShopOrder[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static ShopOrder[]|Proxy[]                          findBy(array $attributes)
 * @method static ShopOrder[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static ShopOrder[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class ShopOrderFactory extends PersistentProxyObjectFactory
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

    public static function class(): string
    {
        return ShopOrder::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'orderDate' => self::faker()->dateTime(),
            'orderStatus' => self::faker()->randomElement(MolliePaymentStatusEnum::cases()),
            // 'orderStatus' => self::faker()->randomElement(MolliePaymentStatusEnum::cases()),
            'ownedBy' => UserFactory::new(),
            'totalAmount' => self::faker()->randomFloat(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ShopOrder $shopOrder): void {})
        ;
    }
}
