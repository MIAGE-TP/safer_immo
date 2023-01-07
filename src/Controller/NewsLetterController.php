<?php

namespace App\Controller;

use App\Entity\NewsLetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Good;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Mailer;
use Symfony\Component\Mime\BodyRendererInterface;
use Knp\Component\Pager\PaginatorInterface;


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

    #[Route('/admin/afficher-les-favoris-envoyes/{id<\d+>}', name: 'favs_sent')]
    public function favSent(NewsLetter $newLetter, EntityManagerInterface $manager, PaginatorInterface $paginator, Request $request): Response
    {
        if ($newLetter) {
            $tab = $newLetter->getFav();
            
            $donnees = [];
            foreach ($tab as $donnee) {
                $good = $manager->getRepository(Good::class)->find($donnee);
                array_push($donnees, $good);
            }
            $favs = $paginator->paginate(
                $donnees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
            );

            return $this->render('admin_dashboard/news_letter/display.html.twig', [
                'favs' => $favs,
                'user' => $newLetter->getUser(),
                'newLetter' => $newLetter
            ]);
        }
    }
}
