<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\GoodCategory;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i= 0; $i < 50; $i++) {
            $category = new GoodCategory();
            $category->setLibelle($this->faker->word())
                     ->setCreatedAt(Carbon::now()->toDateTimeImmutable());

            $manager->persist($category);
        }

        $manager->flush();
    }
}
