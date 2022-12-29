<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\GoodCategory;
use App\Entity\OfferType;
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
        // for ($i= 0; $i < 50; $i++) {
        //     $category = new GoodCategory();
        //     $category->setLibelle($this->faker->word());

        //     $manager->persist($category);
        // }

        // for ($i= 0; $i < 50; $i++) {
        //     $offerType = new OfferType();
        //     $offerType->setLibelle($this->faker->sentence());

        //     $manager->persist($offerType);
        // }

        for ($i= 0; $i < 100; $i++) {
            $department = new Department();
            $department->setName($this->faker->departmentName());

            $manager->persist($department);
        }

        $manager->flush();
    }
}
