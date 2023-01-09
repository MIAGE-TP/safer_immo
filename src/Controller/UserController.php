<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\Mailer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




class UserController extends AbstractController
{
    #[Route('/admin/utilisateurs', name: 'users')]
    public function index(EntityManagerInterface $manager): Response
    {
        $users = $manager->getRepository(User::class)->findWithoutDelete();
        return $this->render('admin_dashboard/user/users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/ajout-utilisateur/', name: 'add_user')]
    public function add(): Response
    {
        return  $this->render('admin_dashboard/user/add.html.twig', [
            'controller_name' => 'UserController'
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

    #[Route('/admin/afficher-les-favoris-par-utilisateur/{id<\d+>}', name: 'user_fav_goods')]
    public function UserWithFavGoods(User $user, PaginatorInterface $paginator, Request $request): Response
    {
        if ($user) {
            $donnees = $user->getGoodsWithFavOnly();

            $favs = $paginator->paginate(
                $donnees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
            );

            return $this->render('admin_dashboard/user/favs.html.twig', [
                'favs' => $favs,
                'user' => $user
            ]);
        }
    }

    #[Route('/mot-de-passe-oublie', name: 'forgotten_password')]
    public function forgottenPass(): Response
    {
        return  $this->render('security/forgotten_password.html.twig', [
            'controller_name' => 'UserController'
        ]);
    }

    #[Route('/update-password', name: 'reset_password', methods:['GET','POST'])]
    public function resetPassword(Request $request, EntityManagerInterface $manager, Mailer $mailer)
    {
        $req = $request->request;
        $user = $manager->getRepository(User::class)->findOneBy(['email' => $req->get('email')]);
        if ($user) {
            $user->setToken($this->generateToken());
            $manager->persist($user);
            $manager->flush();
            $mailer->sendResetPasswordEmail($user->getEmail(), $user->getToken());
            $this->addFlash("success", "Un mail a été envoyé à votre boîte, veuillez le consulter.");
            return $this->redirectToRoute('forgotten_password');
        }
    }

    #[Route('/reset/password/{token}', name: 'new_password')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $manager): Response
    {
        $token = $request->attributes->get('token');
        $user = $manager->getRepository(User::class)->findByToken($token);

        if ($user) {
            return  $this->render('security/new_password.html.twig', [
                'user' => $user
            ]);
        }
    }

    #[Route('/nouveau-mot-de-passe', name: 'brand_new_password')]
    public function newPassword(Request $request, EntityManagerInterface $manager,  UserPasswordHasherInterface $userPasswordHasher)
    {
        $req = $request->request;
        $user = $manager->getRepository(User::class)->find($req->get('user'));
        if ($user) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $req->get('password')
                )
            );
            $user->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Mot de passe modifié!');
            return $this->redirectToRoute('app_login');
        }
    }

    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
