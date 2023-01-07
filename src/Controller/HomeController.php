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
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $manager, Request $request, PaginatorInterface $paginator)
    {
        $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
        $cities = $manager->getRepository(City::class)->findWithoutDelete();
        $offerTypes = $manager->getRepository(OfferType::class)->findWithoutDelete();
        $departments = $manager->getRepository(Department::class)->findWithoutDelete();
        $goods = $manager->getRepository(Good::class)->findAllForHome();

        $donnees = $manager->getRepository(Good::class)->findAllWD();

        $allGoods = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            12 // Nombre de résultats par page
        );
        return $this->render('home/home.html.twig', [
            'categories' => $categories,
            'cities' => $cities,
            'offerTypes' => $offerTypes,
            'departments' => $departments,
            'goods' => $goods,
            'allGoods' => $allGoods
        ]);
    }

}
