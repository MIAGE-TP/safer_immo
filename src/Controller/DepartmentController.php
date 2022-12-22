<?php

namespace App\Controller;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;

class DepartmentController extends AbstractController
{
    #[Route('/admin/departements', name: 'departments')]
    public function index(DepartmentRepository $repository): Response
    {
        $departments = $repository->findWithoutDelete();
        return $this->render('admin_dashboard/department/departments.html.twig', [
            'departments' => $departments
        ]);
    }

    #[Route('/admin/ajout-departement', name: 'add_department')]
    public function new(): Response
    {
        return $this->render('admin_dashboard/department/add.html.twig', [
            'controller_name' => 'DepartmentController',
        ]);
    }

    #[Route('/admin/store-departement', name: 'store_department', methods:['GET','POST'])]
    public function store(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;
        $department = new Department();

        $department->setName($req->get('name'));

        $manager->persist($department);
        $manager->flush();

        return $this->redirectToRoute('departments');
    }
    
    #[Route('/admin/modification-departement/{id<\d+>}', name: 'edit_department')]
    public function edit(int $id, DepartmentRepository $repository): Response
    {
        $department = $repository->find($id);

        if ($department) {
            return  $this->render('admin_dashboard/department/edit.html.twig', [
                'department' => $department
            ]);
        }else {
            throw $this->createNotFoundException(
                "Aucun département ne correspond à l'id ".$id
            );
        }
    }

    #[Route('/admin/update-department/', name: 'update_department')]
    public function update(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;

        $department = $manager->getRepository(Department::class)->find($req->get('id'));

        if ($department) {

            $department->setName($req->get('name'));
            $department->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $manager->flush();
            return $this->redirectToRoute('departments');

        }else {
            throw $this->createNotFoundException(
                "Erreur lors de la modification, merci de réessayer."
            );
        }
    }

    #[Route('/admin/suppression-departement/{id<\d+>}', name: 'delete_department')]
    public function delete(int $id, EntityManagerInterface $manager): Response
    {
        $department = $manager->getRepository(Department::class)->find($id);
        if ($department) {
            if (($department->getCities()->count()) > 0) {
                throw $this->createNotFoundException(
                    "Supression impossible, ce département est lié à plusieurs villes."
                );
            }else {
                $department->setDeletedAt(Carbon::now()->toDateTimeImmutable());
                $manager->flush();
                return $this->redirectToRoute('departments');
            }
        }
    }

}
