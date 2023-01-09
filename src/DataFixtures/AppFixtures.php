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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class AppFixtures extends Fixture
{
    private Generator $faker;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->hasher = $hasher;
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
        $user = new User();
        $user->setEmail('admin@safer.com');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setFirstname('safer');
        $user->setLastname('safer');
        $user->setIsVerified(1);
        $user->setPassword($this->hasher->hashPassword($user, 'Password'));
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
