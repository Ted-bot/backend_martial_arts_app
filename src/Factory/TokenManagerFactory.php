<?php

namespace App\Factory;

use App\Entity\TokenManager;
use App\Repository\TokenManagerRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<TokenManager>
 *
 * @method        TokenManager|Proxy                              create(array|callable $attributes = [])
 * @method static TokenManager|Proxy                              createOne(array $attributes = [])
 * @method static TokenManager|Proxy                              find(object|array|mixed $criteria)
 * @method static TokenManager|Proxy                              findOrCreate(array $attributes)
 * @method static TokenManager|Proxy                              first(string $sortedField = 'id')
 * @method static TokenManager|Proxy                              last(string $sortedField = 'id')
 * @method static TokenManager|Proxy                              random(array $attributes = [])
 * @method static TokenManager|Proxy                              randomOrCreate(array $attributes = [])
 * @method static TokenManagerRepository|ProxyRepositoryDecorator repository()
 * @method static TokenManager[]|Proxy[]                          all()
 * @method static TokenManager[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static TokenManager[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static TokenManager[]|Proxy[]                          findBy(array $attributes)
 * @method static TokenManager[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static TokenManager[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class TokenManagerFactory extends PersistentProxyObjectFactory
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
        return TokenManager::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'tokens' => self::faker()->randomNumber(),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'userProfile' => UserProfileFactory::new(),
            'uuid' => null, // TODO add UUID type manually
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(TokenManager $tokenManager): void {})
        ;
    }
}
