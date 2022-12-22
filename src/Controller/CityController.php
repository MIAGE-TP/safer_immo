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

class CityController extends AbstractController
{
    #[Route('/admin/villes', name: 'cities')]
    public function index(CityRepository $repository): Response
    {
        $cities = $repository->findWithoutDelete();
        return $this->render('admin_dashboard/city/cities.html.twig', [
            'cities' => $cities
        ]);
    }

    #[Route('/admin/ajout-ville', name: 'add_city')]
    public function new(DepartmentRepository $departmentRepository): Response
    {
        $departments = $departmentRepository->findWithoutDelete();
        return $this->render('admin_dashboard/city/add.html.twig', [
            'departments' => $departments
        ]);
    }

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

        return $this->redirectToRoute('cities');
    }

    #[Route('/admin/modification-ville/{id<\d+>}', name: 'edit_city')]
    public function edit(City $city, DepartmentRepository $departmentRepository): Response
    {
        $departments = $departmentRepository->findWithoutDelete();
        if ($city) {
            return  $this->render('admin_dashboard/city/edit.html.twig', [
                'city' => $city,
                'departments' => $departments
            ]);
        }else {
            throw $this->createNotFoundException(
                "Aucune ville ne correspond à l'id ".$city->getId()
            );
        }
    }

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
            return $this->redirectToRoute('cities');

        }else {
            throw $this->createNotFoundException(
                "Erreur lors de la modification, merci de réessayer."
            );
        }
    }

    #[Route('/admin/suppression-ville/{id<\d+>}', name: 'delete_city')]
    public function delete(City $city, EntityManagerInterface $manager): Response
    {
        if ($city) {
            if (($city->getGoods()->count()) > 0) {
                throw $this->createNotFoundException(
                    "Supression impossible, cette ville est liée à plusieurs offres immobilières."
                );
            }else {
                $city->setDeletedAt(Carbon::now()->toDateTimeImmutable());
                $manager->flush();
                return $this->redirectToRoute('cities');
            }
        }
    }
}
