<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\City;
use App\Entity\Department;
use App\Repository\CityRepository;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


class CityController extends AbstractController
{
    /*
    display all cities
    */
    #[Route('/admin/villes', name: 'cities')]
    public function index(CityRepository $repository): Response
    {
        $cities = $repository->findWithoutDelete();
        return $this->render('admin_dashboard/city/cities.html.twig', [
            'cities' => $cities
        ]);
    }

    /* each city relied on a specific departmeny. So to add a new city, we load
    all departments so that the admin can select the department for which to add a new city
    */
    #[Route('/admin/ajout-ville', name: 'add_city')]
    public function new(DepartmentRepository $departmentRepository): Response
    {
        $departments = $departmentRepository->findWithoutDelete();
        return $this->render('admin_dashboard/city/add.html.twig', [
            'departments' => $departments
        ]);
    }

    /*
    addFlash method is used to broadcast notifications with specific message on views
    after adding a new city
    */
    #[Route('/admin/store-ville', name: 'store_city', methods:['GET','POST'])]
    public function store(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;
        $city = new City();
        $department = $manager->getRepository(Department::class)->find($req->get('department'));

        $city->setName($req->get('name'));
        $city->setZipCode($req->get('zip_code'));
        $city->setDepartment($department);

        $manager->persist($city);
        $manager->flush();

        $this->addFlash('success', 'Ville ajout??e!');
        return $this->redirectToRoute('cities');
    }

    /**
     * dipslay the form used to edit a city
     */
    #[Route('/admin/modification-ville/{id<\d+>}', name: 'edit_city')]
    public function edit(City $city, DepartmentRepository $departmentRepository): Response
    {
        $departments = $departmentRepository->findWithoutDelete();
        if ($city) {
            return  $this->render('admin_dashboard/city/edit.html.twig', [
                'city' => $city,
                'departments' => $departments
            ]);
        }
    }

    /**
     * udpate a city adn its details
     */
    #[Route('/admin/update-city/', name: 'update_city')]
    public function update(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;

        $city = $manager->getRepository(City::class)->find($req->get('id'));

        if ($city) {

            $department = $manager->getRepository(Department::class)->find($req->get('department'));

            $city->setName($req->get('name'));
            $city->setZipCode($req->get('zip_code'));
            $city->setDepartment($department);

            $city->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $manager->flush();
            $this->addFlash('success', 'Ville modifi??e!');
            return $this->redirectToRoute('cities');

        }else {
            $this->addFlash('danger', 'Erreur lors de la modification, merci de r??essayer.');
            return $this->redirectToRoute('cities');
        }
    }

    /**
     * destroy a specific city using its id
     */
    #[Route('/admin/suppression-ville/{id<\d+>}', name: 'delete_city')]
    public function delete(City $city, EntityManagerInterface $manager): Response
    {
        if ($city) {
            if (($city->getGoods()->count()) > 0) {
                $this->addFlash('danger', 'Supression impossible, cette ville est li??e ?? plusieurs offres immobili??res.');
                return $this->redirectToRoute('cities');
            }else {
                $city->setDeletedAt(Carbon::now()->toDateTimeImmutable());
                $manager->flush();
                $this->addFlash('success', 'Ville supprim??e!');
                return $this->redirectToRoute('cities');
            }
        }
    }


    /**
     * display all the goods in favorites for a specific city using its id
     */
    #[Route('/admin/afficher-les-favoris-par-ville/{id<\d+>}', name: 'display_city_fav_goods')]
    public function displayCityWithFavGoods(City $city, PaginatorInterface $paginator, Request $request): Response
    {
        if ($city) {
            $donnees = $city->getGoodsWithFavOnly();

            $favs = $paginator->paginate(
                $donnees, // Requ??te contenant les donn??es ?? paginer (ici nos articles)
                $request->query->getInt('page', 1), // Num??ro de la page en cours, pass?? dans l'URL, 1 si aucune page
                10 // Nombre de r??sultats par page
            );

            return $this->render('admin_dashboard/stat/goods.html.twig', [
                'favs' => $favs,
                'ville' => $city->getName()
            ]);
        }
    }
}

