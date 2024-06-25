<?php

namespace App\Factory;

use App\Entity\SubscriptionType;
use App\Repository\SubscriptionTypeRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<SubscriptionType>
 *
 * @method        SubscriptionType|Proxy                              create(array|callable $attributes = [])
 * @method static SubscriptionType|Proxy                              createOne(array $attributes = [])
 * @method static SubscriptionType|Proxy                              find(object|array|mixed $criteria)
 * @method static SubscriptionType|Proxy                              findOrCreate(array $attributes)
 * @method static SubscriptionType|Proxy                              first(string $sortedField = 'id')
 * @method static SubscriptionType|Proxy                              last(string $sortedField = 'id')
 * @method static SubscriptionType|Proxy                              random(array $attributes = [])
 * @method static SubscriptionType|Proxy                              randomOrCreate(array $attributes = [])
 * @method static SubscriptionTypeRepository|ProxyRepositoryDecorator repository()
 * @method static SubscriptionType[]|Proxy[]                          all()
 * @method static SubscriptionType[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static SubscriptionType[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static SubscriptionType[]|Proxy[]                          findBy(array $attributes)
 * @method static SubscriptionType[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static SubscriptionType[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class SubscriptionTypeFactory extends PersistentProxyObjectFactory
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
        return SubscriptionType::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'duration' => self::faker()->text(12),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(SubscriptionType $subscriptionType): void {})
        ;
    }
}
