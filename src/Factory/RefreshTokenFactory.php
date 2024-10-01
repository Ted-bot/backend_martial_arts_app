<?php

namespace App\Factory;

use App\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<RefreshToken>
 *
 * @method        RefreshToken|Proxy                              create(array|callable $attributes = [])
 * @method static RefreshToken|Proxy                              createOne(array $attributes = [])
 * @method static RefreshToken|Proxy                              find(object|array|mixed $criteria)
 * @method static RefreshToken|Proxy                              findOrCreate(array $attributes)
 * @method static RefreshToken|Proxy                              first(string $sortedField = 'id')
 * @method static RefreshToken|Proxy                              last(string $sortedField = 'id')
 * @method static RefreshToken|Proxy                              random(array $attributes = [])
 * @method static RefreshToken|Proxy                              randomOrCreate(array $attributes = [])
 * @method static RefreshTokenRepository|ProxyRepositoryDecorator repository()
 * @method static RefreshToken[]|Proxy[]                          all()
 * @method static RefreshToken[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static RefreshToken[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static RefreshToken[]|Proxy[]                          findBy(array $attributes)
 * @method static RefreshToken[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static RefreshToken[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class RefreshTokenFactory extends PersistentProxyObjectFactory
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
        return RefreshToken::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'refreshToken' => self::faker()->text(128),
            'username' => self::faker()->text(255),
            'valid' => self::faker()->dateTime(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(RefreshToken $refreshToken): void {})
        ;
    }
}
