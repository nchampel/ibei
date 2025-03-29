<?php

namespace App\Factory;

use App\Entity\Purchase;
use App\Enum\PurchaseType;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Purchase>
 *
 * @method        Purchase|Proxy create(array|callable $attributes = [])
 * @method static Purchase|Proxy createOne(array $attributes = [])
 * @method static Purchase|Proxy find(object|array|mixed $criteria)
 * @method static Purchase|Proxy findOrCreate(array $attributes)
 * @method static Purchase|Proxy first(string $sortedField = 'id')
 * @method static Purchase|Proxy last(string $sortedField = 'id')
 * @method static Purchase|Proxy random(array $attributes = [])
 * @method static Purchase|Proxy randomOrCreate(array $attributes = [])
 * @method static PurchaseRepository|ProxyRepositoryDecorator repository()
 * @method static Purchase[]|Proxy[] all()
 * @method static Purchase[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Purchase[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Purchase[]|Proxy[] findBy(array $attributes)
 * @method static Purchase[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Purchase[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class PurchaseFactory extends PersistentProxyObjectFactory{
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
        return Purchase::class;
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
            'product' => ProductInfosFactory::random(),
            'type' => self::faker()->randomElement(['achetable', 'acheté']),
            'user' => UserFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Purchase $purchase): void {})
        ;
    }
}
