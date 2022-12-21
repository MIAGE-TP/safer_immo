<?php

namespace App\Controller;

use App\Entity\GoodCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GoodCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class GoodCategoryController extends AbstractController
{
    #[Route('/admin/categories-bien', name: 'good_types')]
    public function index(GoodCategoryRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->render('admin_dashboard/good_types.html.twig', [
            'categories' => $categories
        ]);
    }
    
    #[Route('/admin/ajout-categorie-bien', name: 'add_good_type')]
    public function new(): Response
    {
        return $this->render('admin_dashboard/good_type/add.html.twig', [
            'controller_name' => 'GoodTypeController',
        ]);
    }

    #[Route('/admin/store-categorie-bien', name: 'store_good_type', methods:['GET','POST'])]
    public function store(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;
        $category = new GoodCategory();
        $category->setLibelle($req->get('libelle'));
        $manager->persist($category);
        $manager->flush();
        return $this->render('admin_dashboard/good_type/add.html.twig', [
            'controller_name' => 'GoodTypeController',
        ]);
    }
   
}
