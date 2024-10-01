<?php

namespace App\Factory;

use App\Entity\PasswordToken;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<PasswordToken>
 *
 * @method        PasswordToken|Proxy                       create(array|callable $attributes = [])
 * @method static PasswordToken|Proxy                       createOne(array $attributes = [])
 * @method static PasswordToken|Proxy                       find(object|array|mixed $criteria)
 * @method static PasswordToken|Proxy                       findOrCreate(array $attributes)
 * @method static PasswordToken|Proxy                       first(string $sortedField = 'id')
 * @method static PasswordToken|Proxy                       last(string $sortedField = 'id')
 * @method static PasswordToken|Proxy                       random(array $attributes = [])
 * @method static PasswordToken|Proxy                       randomOrCreate(array $attributes = [])
 * @method static EntityRepository|ProxyRepositoryDecorator repository()
 * @method static PasswordToken[]|Proxy[]                   all()
 * @method static PasswordToken[]|Proxy[]                   createMany(int $number, array|callable $attributes = [])
 * @method static PasswordToken[]|Proxy[]                   createSequence(iterable|callable $sequence)
 * @method static PasswordToken[]|Proxy[]                   findBy(array $attributes)
 * @method static PasswordToken[]|Proxy[]                   randomRange(int $min, int $max, array $attributes = [])
 * @method static PasswordToken[]|Proxy[]                   randomSet(int $number, array $attributes = [])
 */
final class PasswordTokenFactory extends PersistentProxyObjectFactory
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
        return PasswordToken::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'expiresAt' => self::faker()->dateTime(),
            'token' => self::faker()->text(50),
            'user' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(PasswordToken $passwordToken): void {})
        ;
    }
}
