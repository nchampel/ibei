<?php

namespace App\DataFixtures;

use App\Factory\PotFactory;
use App\Factory\ProductFactory;
use App\Factory\ProductInfosFactory;
use App\Factory\PurchaseFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        // ProductFactory::createMany(10);
        UserFactory::createMany(2);
        ProductInfosFactory::createMany(5);
        PurchaseFactory::createMany(10);
        PotFactory::createMany(5);

        //$manager->flush();
    }
}
