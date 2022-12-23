<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Mailer;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Mailer $mailer): Response
    {
        $req = $request->request;
        $email = $req->get('email');
        if ($email) {
            $existingUser = $entityManager->getRepository(User::class)->findByEmail($email);

            if ($existingUser) {
                $this->addFlash('danger', 'Cette adresse mail est déjà utilisée par un utlisateur.');
                return $this->redirectToRoute('app_register');
            } else {
                $user = new User();
                // encode the password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $req->get('password')
                    )
                );
                $user->setFirstname($req->get('firstname'));
                $user->setLastname($req->get('lastname'));

                $user->setEmail($email);
                $user->setToken($this->generateToken());
                $entityManager->persist($user);
                $entityManager->flush();

                $mailer->sendEmailConfirmation($user->getEmail(), $user->getToken());
                $this->addFlash("success", "Inscription réussie! Consultez votre boîte, un mail a été envoyé à votre adresse.");
            }
        }

        return $this->render('registration/register.html.twig', [
            'controller_name' => 'RegistrationController'
        ]);
    }

    #[Route('/verify/email/{token}', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator): Response
    {
        $token = $request->attributes->get('token');
        $user = $entityManager->getRepository(User::class)->findByToken($token);

        if ($user) {
            $user->setToken(null);
            $user->setIsVerified(true);
            $user->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $this->addFlash("success", "Adresse email validée! Votre compte est actif.");
            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('profile');
        }else {
            $this->addFlash('danger', 'Adresse email non vérifiée');
            return $this->redirectToRoute('app_register');
        }
    }

    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
