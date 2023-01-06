<?php

namespace App\Controller;

use App\Entity\NewsLetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Mailer;
use Carbon\Carbon;
use Symfony\Component\Mime\BodyRendererInterface;


class NewsLetterController extends AbstractController
{
    #[Route('admin/favoris-envoyes', name: 'app_news_letter')]
    public function index(EntityManagerInterface $manager): Response
    {
        $newLetters = $manager->getRepository(NewsLetter::class)->findAll();

        return $this->render('admin_dashboard/news_letter/news.html.twig', [
            'newLetters' => $newLetters
        ]);
    }

    #[Route('admin/envoyer-les-favoris/{id<\d+>}', name: 'send_favs')]
    public function add(User $user, EntityManagerInterface $entityManager, Mailer $mailer, Request $request, BodyRendererInterface $bodyRenderer): Response
    {
        if ($user) {
            $newL = new NewsLetter();
            $newL->setUser($user);
            $newL->setFav($user->getFavGoodIds());
            $entityManager->persist($newL);
            $entityManager->flush();
            $mailer->sendFavorites($user, $user->getGoodsWithFavOnly(), $bodyRenderer);
            $this->addFlash("success", "Favori(s) envoyé(s) à l'utilisateur!");
        }
        
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }


    #[Route('/admin/suppression-newsLetter/{id<\d+>}', name: 'delete_newletter')]
    public function delete(NewsLetter $newLetter, EntityManagerInterface $manager): Response
    {
        if ($newLetter) {
            $manager->remove($newLetter);
            $manager->flush();
            $this->addFlash('success', 'Envoi supprimé!');
            return $this->redirectToRoute('app_news_letter');
        } else {
            $this->addFlash('danger', 'Suppression impossible');
            return $this->redirectToRoute('app_news_letter');
        }
    }
}
