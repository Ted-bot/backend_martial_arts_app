<?php

namespace App\Factory;

use App\Entity\PostEvent;
use Zenstruck\Foundry\Proxy;
use App\Factory\UserProfileFactory;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\RepositoryProxy;
use App\Repository\PostEventRepository;

/**
 * @extends ModelFactory<PostEvent>
 *
 * @method        PostEvent|Proxy                     create(array|callable $attributes = [])
 * @method static PostEvent|Proxy                     createOne(array $attributes = [])
 * @method static PostEvent|Proxy                     find(object|array|mixed $criteria)
 * @method static PostEvent|Proxy                     findOrCreate(array $attributes)
 * @method static PostEvent|Proxy                     first(string $sortedField = 'id')
 * @method static PostEvent|Proxy                     last(string $sortedField = 'id')
 * @method static PostEvent|Proxy                     random(array $attributes = [])
 * @method static PostEvent|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PostEventRepository|RepositoryProxy repository()
 * @method static PostEvent[]|Proxy[]                 all()
 * @method static PostEvent[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static PostEvent[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static PostEvent[]|Proxy[]                 findBy(array $attributes)
 * @method static PostEvent[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static PostEvent[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PostEventFactory extends ModelFactory
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

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->title(),
            'description' => self::faker()->sentence(2, true),
            'relatedUser' => UserProfileFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(PostEvent $postEvent): void {})
        ;
    }

    protected static function getClass(): string
    {
        return PostEvent::class;
    }
}
