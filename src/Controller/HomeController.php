<?php

namespace App\Controller;

use App\Entity\GoodCategory;
use App\Entity\OfferType;
use App\Entity\City;
use App\Entity\Department;
use App\Entity\Good;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $manager)
    {
        $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
        $cities = $manager->getRepository(City::class)->findWithoutDelete();
        $offerTypes = $manager->getRepository(OfferType::class)->findWithoutDelete();
        $departments = $manager->getRepository(Department::class)->findWithoutDelete();
        $goods = $manager->getRepository(Good::class)->findAllForHome();

        return $this->render('home/home.html.twig', [
            'categories' => $categories,
            'cities' => $cities,
            'offerTypes' => $offerTypes,
            'departments' => $departments,
            'goods' => $goods
        ]);
    }

}
