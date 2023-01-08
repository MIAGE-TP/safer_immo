<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\GoodCategory;
use App\Entity\OfferType;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;


class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {

        // load department
        for ($i= 0; $i < 100; $i++) {
            $department = new Department();
            $department->setName($this->faker->departmentName());

            $manager->persist($department);
        }
        // load department end

        // load default admin user
        $admin = [
            [
                'email' => 'admin@safer.com',
                'role' => ["ROLE_ADMIN"],
                'firstname' => 'safer',
                'lastname' => 'safer',
                'isVerfied' => 1,
                'password' => '$2y$13$NdLB1IlFY6YKoScPi7bWh.h3kXy7A3p.2JxLjZyXYXKF1qc2xO96C'
            ]
        ];

        $user = new User();
        $user->setEmail($admin[0]['email']);
        $user->setRoles($admin[0]['role']);
        $user->setFirstname($admin[0]['firstname']);
        $user->setLastname($admin[0]['lastname']);
        $user->setIsVerified($admin[0]['isVerfied']);
        $user->setPassword($admin[0]['password']);
        $manager->persist($user);
        // load default admin user end

        // load offerType
        $offerTypes = [
            ['libelle' => 'Location'],
            ['libelle' => 'Vente']
        ];

        for ($i= 0; $i < count($offerTypes); $i++) {
            $offerType = new OfferType();
            $offerType->setLibelle($offerTypes[$i]['libelle']);

            $manager->persist($offerType);
        }
        // loaf offertType end

        //load goodCategory
        $goodCategories = [
            ['libelle' => 'Terrain agricole'],
            ['libelle' => 'Prairie'],
            ['libelle' => 'Bois'],
            ['libelle' => 'Bâtiments'],
            ['libelle' => 'Exploitations'],
        ];
        for ($i= 0; $i < count($goodCategories); $i++) {
            $category = new GoodCategory();
            $category->setLibelle($goodCategories[$i]['libelle']);

            $manager->persist($category);
        }
        //load goodCategory end


        $manager->flush();
    }
}
