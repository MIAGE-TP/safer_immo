<?php

namespace App\Controller;


use App\Entity\GoodCategory;
use App\Entity\City;
use App\Entity\Department;
use App\Entity\OfferType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GoodCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Carbon\Carbon;
use Knp\Component\Pager\PaginatorInterface;

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
        $this->addFlash('success', 'Catégorie de bien ajoutée!');
        return $this->redirectToRoute('good_types');
    }
    
    #[Route('/admin/modification-categorie-bien/{id<\d+>}', name: 'edit_good_type')]
    public function edit(GoodCategory $category): Response
    {
        if ($category) {
            return  $this->render('admin_dashboard/good_type/edit.html.twig', [
                'category' => $category
            ]);
        }else {
            throw $this->createNotFoundException(
                "Aucune catégorie ne correspond à l'id ".$category->getId()
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
            $this->addFlash('success', 'Catégorie modifiée!');
            return $this->redirectToRoute('good_types');

        }else {
            throw $this->createNotFoundException(
                "Erreur lors de la modification, merci de réessayer."
            );
        }
    }

    #[Route('/admin/suppression-categorie-bien/{id<\d+>}', name: 'delete_good_type')]
    public function delete(GoodCategory $category, EntityManagerInterface $manager): Response
    {
        if ($category) {
            if (($category->getGoods()->count()) > 0) {
                throw $this->createNotFoundException(
                    "Supression impossible, cette catégorie de bien est utilisée par un ou plusieurs biens."
                );
            }else {
                $category->setDeletedAt(Carbon::now()->toDateTimeImmutable());
                $manager->flush();
                $this->addFlash('success', 'Suppression de catégorie effectuée!');
                return $this->redirectToRoute('good_types');
            }
        }
    }

    #[Route('/admin/afficher-les-favoris-par-categorie/{id<\d+>}', name: 'category_fav_goods')]
    public function CatWithFavGoods(GoodCategory $category, PaginatorInterface $paginator, Request $request): Response
    {
        if ($category) {
            $donnees = $category->getGoodsWithFavOnly();

            $favs = $paginator->paginate(
                $donnees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                1 // Nombre de résultats par page
            );

            return $this->render('admin_dashboard/stat/goods.html.twig', [
                'favs' => $favs,
                'categorie' => $category->getLibelle(),
            ]);
        }
    }

    #[Route('/biens-par-catégorie/{slug}', name: 'goods_by_cat')]
    public function GoodsByCat(PaginatorInterface $paginator, Request $request, EntityManagerInterface $manager): Response
    {
        $slug = $request->attributes->get('slug');
        $category = $manager->getRepository(GoodCategory::class)->findOneBy(['libelle'=> $slug]);
        $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
        $cities = $manager->getRepository(City::class)->findWithoutDelete();
        $offerTypes = $manager->getRepository(OfferType::class)->findWithoutDelete();
        $departments = $manager->getRepository(Department::class)->findWithoutDelete();

        if ($category) {
            $donnees = $category->getGoods();

            $goods = $paginator->paginate(
                $donnees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                12 // Nombre de résultats par page
            );

            return $this->render('public/goods_per_cat.html.twig', [
                'goods' => $goods,
                'categories' => $categories,
                'categorie' => $category->getLibelle(),
                'cities' => $cities,
                'offerTypes' => $offerTypes,
                'departments' => $departments
            ]);
        }else {
            return $this->redirectToRoute('home');
        }
    }
}
