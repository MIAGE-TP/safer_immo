<?php

namespace App\Controller;


use App\Entity\GoodCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GoodCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Carbon\Carbon;

use function PHPUnit\Framework\isNull;

class GoodCategoryController extends AbstractController
{
    #[Route('/admin/categories-bien', name: 'good_types')]
    public function index(GoodCategoryRepository $repository): Response
    {
        $categories = $repository->findWithoutDelete();
        return $this->render('admin_dashboard/good_type/good_types.html.twig', [
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

        return $this->redirectToRoute('good_types');
    }
    
    #[Route('/admin/modification-categorie-bien/{id<\d+>}', name: 'edit_good_type')]
    public function edit(int $id, GoodCategoryRepository $repository): Response
    {
        $category = $repository->find($id);

        if ($category) {
            return  $this->render('admin_dashboard/good_type/edit.html.twig', [
                'category' => $category
            ]);
        }else {
            throw $this->createNotFoundException(
                "Aucune catégorie ne correspond à l'id ".$id
            );
        }
    }

    #[Route('/admin/update-categorie-bien/', name: 'update_good_type')]
    public function update(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;

        $category = $manager->getRepository(GoodCategory::class)->find($req->get('id'));

        if ($category) {

            $category->setLibelle($req->get('libelle'));
            $category->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $manager->flush();
            return $this->redirectToRoute('good_types');

        }else {
            throw $this->createNotFoundException(
                "Erreur lors de la modification, merci de réessayer."
            );
        }
    }

    #[Route('/admin/suppression-categorie-bien/{id<\d+>}', name: 'delete_good_type')]
    public function delete(int $id, EntityManagerInterface $manager): Response
    {
        $category = $manager->getRepository(GoodCategory::class)->find($id);
        if ($category) {
            if (($category->getGoods()->count()) > 0) {
                throw $this->createNotFoundException(
                    "Supression impossible, cette catégorie de bien est utilisée par un ou plusieurs biens."
                );
            }else {
                $category->setDeletedAt(Carbon::now()->toDateTimeImmutable());
                $manager->flush();
                return $this->redirectToRoute('good_types');
            }
        }
    }
}
