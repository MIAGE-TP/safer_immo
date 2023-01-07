<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Good;
use App\Entity\GoodCategory;
use App\Entity\OfferType;
use App\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Knp\Component\Pager\PaginatorInterface;
use Carbon\Carbon;

class GoodController extends AbstractController
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    #[Route('/admin/biens', name: 'goods')]
    public function index(EntityManagerInterface $manager, Request $request, PaginatorInterface $paginator)
    {
        $donnees = $manager->getRepository(Good::class)->findAllForAdmin();
        
        $goods = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );
        return $this->render('admin_dashboard/good/goods.html.twig', [
            'goods' => $goods
        ]);
    }

    #[Route('/admin/ajout-bien', name: 'add_good')]
    public function add(EntityManagerInterface $manager): Response
    {
        $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
        $cities = $manager->getRepository(City::class)->findWithoutDelete();
        $offerTypes = $manager->getRepository(OfferType::class)->findWithoutDelete();
        $departments = $manager->getRepository(Department::class)->findWithoutDelete();
        
        return $this->render('admin_dashboard/good/add.html.twig', [
            'categories' => $categories,
            'departments' => $departments,
            'cities' => $cities,
            'offerTypes' => $offerTypes
        ]);
    }

    #[Route('/admin/store-bien', name: 'store_good')]
    public function store(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger):Response
    {
        $dir = $this->getParameter('offer_galery');
        $manager->getRepository(Good::class)->insert($request, $manager, $slugger, $dir);
        $this->addFlash('success', 'Offre immobilière ajoutée!');
        return $this->redirectToRoute('goods');
    }


    #[Route('/biens/{slug}', name: 'show_good')]
    public function show(EntityManagerInterface $manager, $slug):Response
    {

        $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
        $cities = $manager->getRepository(City::class)->findWithoutDelete();
        $offerTypes = $manager->getRepository(OfferType::class)->findWithoutDelete();
        $departments = $manager->getRepository(Department::class)->findWithoutDelete();
        
        $good = $manager->getRepository(Good::class)->findOneBy(['slug'=> $slug]);
        if ($good) {
            return  $this->render('public/display_good.html.twig', [
                'good' => $good,
                'categories' => $categories,
                'departments' => $departments,
                'cities' => $cities,
                'offerTypes' => $offerTypes
            ]);
        }
    }

    #[Route('/admin/modification-bien/{id<\d+>}', name: 'edit_good')]
    public function edit(Good $good, EntityManagerInterface $manager):Response
    {
        if ($good) {
            $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
            $cities = $manager->getRepository(City::class)->findWithoutDelete();
            $offerTypes = $manager->getRepository(OfferType::class)->findWithoutDelete();
            $departments = $manager->getRepository(Department::class)->findWithoutDelete();

            return  $this->render('admin_dashboard/good/edit.html.twig', [
                'good' => $good,
                'categories' => $categories,
                'departments' => $departments,
                'cities' => $cities,
                'offerTypes' => $offerTypes
            ]);
        }
    }

    #[Route('/admin/update-good/', name: 'update_good')]
    public function update(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger):Response
    {
        $dir = $this->getParameter('offer_galery');
        $manager->getRepository(Good::class)->update($request, $manager, $slugger, $dir);
        $this->addFlash('success', 'Offre immobilière mise à jour!');
        return $this->redirectToRoute('goods');
    }

    #[Route('/admin/suppression-bien/{id<\d+>}', name: 'delete_good')]
    public function delete(Good $good, EntityManagerInterface $manager):Response
    {
        if ($good) {
            $good->setDeletedAt(Carbon::now()->toDateTimeImmutable());
            $manager->flush();
            $this->addFlash('success', 'Offre supprimée!');
            return $this->redirectToRoute('goods');
        }
    }


    #[Route('/admin/masquer-bien/{id<\d+>}', name: 'hide_good')]
    public function hide(Good $good, EntityManagerInterface $manager):Response
    {
        if ($good) {
            if ($good->isHidden()) {
                $good->setHidden(false);
                $this->addFlash('success', 'Cette offre est de nouveau visible pour les utilisateurs!');
            } else {
                $good->setHidden(true);
                $this->addFlash('success', 'Cette offre a été masquée. Elle ne sera plus visible aux utilisateurs.');
            }
            $good->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $manager->flush();
           
            return $this->redirectToRoute('goods');
        }
    }

    #[Route('/trouver-des-biens/', name: 'search_goods')]
    public function search(Request $request, EntityManagerInterface $manager, PaginatorInterface $paginator):Response
    {
        
        $req = $request->request;
        $donnees =  $manager->getRepository(Good::class)->search($req);
        $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
        $cities = $manager->getRepository(City::class)->findWithoutDelete();
        $offerTypes = $manager->getRepository(OfferType::class)->findWithoutDelete();
        $departments = $manager->getRepository(Department::class)->findWithoutDelete();
       
        $goods = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            12 // Nombre de résultats par page
        );
        return  $this->render('public/search_results.html.twig', [
            'goods' => $goods,
            'categories' => $categories,
            'cities' => $cities,
            'offerTypes' => $offerTypes,
            'departments' => $departments
        ]);
    }
}
