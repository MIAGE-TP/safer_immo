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
    #[Route('admin/news-letter', name: 'app_news_letter')]
    public function index(): Response
    {
        return $this->render('news_letter/index.html.twig', [
            'controller_name' => 'NewsLetterController',
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
            $mailer->sendFavorites($user, $user->getGoodsWithFavOnly(),$bodyRenderer);
            $this->addFlash("success", "Favoris envoyé à l'utilisateur!");
        }
        
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }
}
