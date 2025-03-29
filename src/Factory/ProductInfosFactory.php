<?php

namespace App\Factory;

use App\Entity\ProductInfos;
use App\Repository\ProductInfosRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<ProductInfos>
 *
 * @method        ProductInfos|Proxy create(array|callable $attributes = [])
 * @method static ProductInfos|Proxy createOne(array $attributes = [])
 * @method static ProductInfos|Proxy find(object|array|mixed $criteria)
 * @method static ProductInfos|Proxy findOrCreate(array $attributes)
 * @method static ProductInfos|Proxy first(string $sortedField = 'id')
 * @method static ProductInfos|Proxy last(string $sortedField = 'id')
 * @method static ProductInfos|Proxy random(array $attributes = [])
 * @method static ProductInfos|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductInfosRepository|ProxyRepositoryDecorator repository()
 * @method static ProductInfos[]|Proxy[] all()
 * @method static ProductInfos[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductInfos[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static ProductInfos[]|Proxy[] findBy(array $attributes)
 * @method static ProductInfos[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ProductInfos[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class ProductInfosFactory extends PersistentProxyObjectFactory{
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
        return ProductInfos::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'cooldown' => self::faker()->numberBetween(10,90),
            'gain' => self::faker()->numberBetween(100,1000),
            'name' => self::faker()->text(25),
            'price' => self::faker()->numberBetween(100,10000),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ProductInfos $productInfos): void {})
        ;
    }
}
