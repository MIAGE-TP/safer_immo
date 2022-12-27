<?php

namespace App\Controller;


use App\Entity\Good;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CityRepository;
use App\Repository\DepartmentRepository;
use App\Repository\OfferTypeRepository;
use App\Repository\GoodCategoryRepository;
use App\Repository\GoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Knp\Component\Pager\PaginatorInterface;

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
    public function index(GoodRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->security->getUser();
        $donnees = $repository->findWithoutDelete($user);
        
        $goods = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            1 // Nombre de résultats par page
        );
        return $this->render('admin_dashboard/good/goods.html.twig', [
            'goods' => $goods
        ]);
    }

    #[Route('/admin/ajout-bien', name: 'add_good')]
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
        return $this->redirectToRoute('goods');
    }
}
