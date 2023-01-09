<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Department;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;


class DepartmentController extends AbstractController
{
    /*
    display all departments
    */
    #[Route('/admin/departements', name: 'departments')]
    public function index(DepartmentRepository $repository): Response
    {
        $departments = $repository->findWithoutDelete();
        return $this->render('admin_dashboard/department/departments.html.twig', [
            'departments' => $departments
        ]);
    }

    /*
    display the form to add a new departement
    */
    #[Route('/admin/ajout-departement', name: 'add_department')]
    public function new(): Response
    {
        return $this->render('admin_dashboard/department/add.html.twig', [
            'controller_name' => 'DepartmentController'
        ]);
    }

    /*
    save the new department
    */
    #[Route('/admin/store-departement', name: 'store_department', methods:['GET','POST'])]
    public function store(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;
        $department = new Department();

        $department->setName($req->get('name'));

        $manager->persist($department);
        $manager->flush();

        $this->addFlash('success', 'Département ajouté!');
        return $this->redirectToRoute('departments');
    }
    
    /*
        display the form to update a specific department
    */
    #[Route('/admin/modification-departement/{id<\d+>}', name: 'edit_department')]
    public function edit(Department $department): Response
    {
        if ($department) {
            return  $this->render('admin_dashboard/department/edit.html.twig', [
                'department' => $department
            ]);
        }else {
            throw $this->createNotFoundException(
                "Aucun département ne correspond à l'id ".$department->getId()
            );
        }
    }

    /*
        upate a specific department adn its details
    */
    #[Route('/admin/update-departement/', name: 'update_department')]
    public function update(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;

        $department = $manager->getRepository(Department::class)->find($req->get('id'));

        if ($department) {

            $department->setName($req->get('name'));
            $department->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $manager->flush();

            $this->addFlash('success', 'Département modifié!');
            return $this->redirectToRoute('departments');

        }else {
            throw $this->createNotFoundException(
                "Erreur lors de la modification, merci de réessayer."
            );
        }
    }

    #[Route('/admin/suppression-departement/{id<\d+>}', name: 'delete_department')]
    public function delete(Department $department, EntityManagerInterface $manager): Response
    {
        if ($department) {
            if (($department->getCities()->count()) > 0) {
                throw $this->createNotFoundException(
                    "Supression impossible, ce département est lié à plusieurs villes."
                );
            }else {
                $department->setDeletedAt(Carbon::now()->toDateTimeImmutable());
                $manager->flush();

                $this->addFlash('success', 'Département supprimé!');
                return $this->redirectToRoute('departments');
            }
        }
    }

    /* Ajax request handler to load cities according to a selected department and return a serialized json response
     containing only the needed data. We used groups to define which data to send back in the response.
     Only the fields having the annotation with group main in the City entity are going to be taken.
    */
    #[Route('/ajax-location', name: 'ajax_location')]
    public function loadCities(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $data = $request->query;
        if ($data->get('type') == "department") {
            $cities = $manager->getRepository(City::class)->getCities($data->get('value'));
            
            $response = $serializer->serialize($cities, 'json', ['groups' => 'main']);
            
            return JsonResponse::fromJsonString($response);
        }
    }

    #[Route('/admin/afficher-les-favoris-par-departement/{id<\d+>}', name: 'department_fav_goods')]
    public function DepartmentWithFavGoods(Department $department, PaginatorInterface $paginator, Request $request): Response
    {
        if ($department) {
            $donnees = $department->getGoodsWithFavOnly();

            $favs = $paginator->paginate(
                $donnees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
            );

            return $this->render('admin_dashboard/stat/goods.html.twig', [
                'favs' => $favs,
                'departement' => $department->getName()
            ]);
        }
    }
}
