<?php

namespace App\Factory;

use App\Entity\Pot;
use App\Enum\PotType;
use App\Repository\PotRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Pot>
 *
 * @method        Pot|Proxy create(array|callable $attributes = [])
 * @method static Pot|Proxy createOne(array $attributes = [])
 * @method static Pot|Proxy find(object|array|mixed $criteria)
 * @method static Pot|Proxy findOrCreate(array $attributes)
 * @method static Pot|Proxy first(string $sortedField = 'id')
 * @method static Pot|Proxy last(string $sortedField = 'id')
 * @method static Pot|Proxy random(array $attributes = [])
 * @method static Pot|Proxy randomOrCreate(array $attributes = [])
 * @method static PotRepository|ProxyRepositoryDecorator repository()
 * @method static Pot[]|Proxy[] all()
 * @method static Pot[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Pot[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Pot[]|Proxy[] findBy(array $attributes)
 * @method static Pot[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Pot[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class PotFactory extends PersistentProxyObjectFactory{
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
        return Pot::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'gain' => self::faker()->numberBetween(10,1000),
            'isClaimed' => self::faker()->boolean(0.5),
            'type' => self::faker()->randomElement(['pot', 'jackpot']),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Pot $pot): void {})
        ;
    }
}
