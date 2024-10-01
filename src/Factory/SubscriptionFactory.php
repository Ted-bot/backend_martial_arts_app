<?php

namespace App\Factory;

use App\Entity\Subscription;
use App\Enum\MolliePaymentStatusEnum;
use App\Enum\SubscriptionLengthTypeEnum;
use App\Repository\SubscriptionRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Subscription>
 *
 * @method        Subscription|Proxy                              create(array|callable $attributes = [])
 * @method static Subscription|Proxy                              createOne(array $attributes = [])
 * @method static Subscription|Proxy                              find(object|array|mixed $criteria)
 * @method static Subscription|Proxy                              findOrCreate(array $attributes)
 * @method static Subscription|Proxy                              first(string $sortedField = 'id')
 * @method static Subscription|Proxy                              last(string $sortedField = 'id')
 * @method static Subscription|Proxy                              random(array $attributes = [])
 * @method static Subscription|Proxy                              randomOrCreate(array $attributes = [])
 * @method static SubscriptionRepository|ProxyRepositoryDecorator repository()
 * @method static Subscription[]|Proxy[]                          all()
 * @method static Subscription[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Subscription[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Subscription[]|Proxy[]                          findBy(array $attributes)
 * @method static Subscription[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Subscription[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class SubscriptionFactory extends PersistentProxyObjectFactory
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
        return Subscription::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'amount' => self::faker()->randomFloat(),
            'createdAt' => self::faker()->dateTime(),
            'dateEnd' => self::faker()->dateTime(),
            'dateStart' => self::faker()->dateTime(),
            'duration' => self::faker()->randomElement(SubscriptionLengthTypeEnum::cases()),
            'status' => self::faker()->randomElement(MolliePaymentStatusEnum::cases()),
            'subscribedProduct' => ProductFactory::new(),
            'subscriptionOwnedBy' => UserFactory::new(),
            'uuid' => null, // TODO add UUID type manually
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Subscription $subscription): void {})
        ;
    }
}
