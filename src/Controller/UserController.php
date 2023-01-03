<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;




class UserController extends AbstractController
{
    #[Route('/admin/utilisateurs', name: 'users')]
    public function index(EntityManagerInterface $manager): Response
    {
        $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
        $users = $manager->getRepository(User::class)->findWithoutDelete();
        return $this->render('admin_dashboard/user/users.html.twig', [
            'users' => $users,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/modification-utilisateur/{id<\d+>}', name: 'edit_user')]
    public function edit(User $user): Response
    {
        if ($user) {
            return  $this->render('admin_dashboard/user/edit.html.twig', [
                'user' => $user,
            ]);
        }
    }


    #[Route('/admin/update-user/', name: 'update_user')]
    public function update(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;

        $user = $manager->getRepository(User::class)->find($req->get('id'));

        if ($user) {
            $user->setFirstname($req->get('firstname'));
            $user->setLastname($req->get('lastname'));
            $user->setRoles($req->all('role'));
            $user->setEmail($req->get('email'));

            $user->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $manager->flush();
            $this->addFlash('success', 'Utilisateur modifié!');
            return $this->redirectToRoute('users');

        }else {
            throw $this->createNotFoundException(
                "Erreur lors de la modification, merci de réessayer."
            );
        }
    }
}
