<?php

namespace App\Factory;

use App\Enum\MolliePaymentStatusEnum;
use App\Entity\StatusTransfer;
use App\Repository\StatusTransferRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<StatusTransfer>
 *
 * @method        StatusTransfer|Proxy                              create(array|callable $attributes = [])
 * @method static StatusTransfer|Proxy                              createOne(array $attributes = [])
 * @method static StatusTransfer|Proxy                              find(object|array|mixed $criteria)
 * @method static StatusTransfer|Proxy                              findOrCreate(array $attributes)
 * @method static StatusTransfer|Proxy                              first(string $sortedField = 'id')
 * @method static StatusTransfer|Proxy                              last(string $sortedField = 'id')
 * @method static StatusTransfer|Proxy                              random(array $attributes = [])
 * @method static StatusTransfer|Proxy                              randomOrCreate(array $attributes = [])
 * @method static StatusTransferRepository|ProxyRepositoryDecorator repository()
 * @method static StatusTransfer[]|Proxy[]                          all()
 * @method static StatusTransfer[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static StatusTransfer[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static StatusTransfer[]|Proxy[]                          findBy(array $attributes)
 * @method static StatusTransfer[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static StatusTransfer[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class StatusTransferFactory extends PersistentProxyObjectFactory
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
        return StatusTransfer::class;
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
            // 'status' => self::faker()->randomElement(MolliePaymentStatusEnum::cases()),
            'status' => self::faker()->randomElement(MolliePaymentStatusEnum::OPEN),
            // 'transferId' => self::faker()->text(15),
            'transferId' => 'tr_DBPsz4sq7M',
            'userOrder' => ShopOrderFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(StatusTransfer $statusTransfer): void {})
        ;
    }
}
