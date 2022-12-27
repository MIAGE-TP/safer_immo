<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Department;
use App\Entity\Good;
use App\Entity\GoodCategory;
use App\Entity\OfferGalery;
use App\Entity\OfferType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CityRepository;
use App\Repository\DepartmentRepository;
use App\Repository\OfferTypeRepository;
use App\Repository\GoodCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class GoodController extends AbstractController
{
    #[Route('/ajout-bien', name: 'add_good')]
    public function add(DepartmentRepository $departmentRepository, GoodCategoryRepository $catRepository,
                          CityRepository $cityRepository, OfferTypeRepository $offTypRepository
    ): Response
    {
        $categories = $catRepository->findWithoutDelete();
        $departments = $departmentRepository->findWithoutDelete();
        $cities = $cityRepository->findWithoutDelete();
        $offerTypes = $offTypRepository->findWithoutDelete();
        return $this->render('admin_dashboard/good/add.html.twig', [
            'categories' => $categories,
            'departments' => $departments,
            'cities' => $cities,
            'offerTypes' => $offerTypes
        ]);
    }

    #[Route('/store-bien', name: 'store_good')]
    public function store(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger):Response
    {
        $dir = $this->getParameter('offer_galery');
        $manager->getRepository(Good::class)->insert($request, $manager, $slugger, $dir);
        $this->addFlash('success', 'Offre immobilière ajoutée!');
        return $this->redirectToRoute('add_good');
    }
}
