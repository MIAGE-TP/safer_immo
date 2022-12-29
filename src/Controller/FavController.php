<?php

namespace App\Controller;

use App\Entity\Fav;
use App\Repository\FavRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Carbon\Carbon;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Good;


class FavController extends AbstractController
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    #[Route('/mes-favoris', name: 'favs')]
    public function index(FavRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $user = $this->security->getUser();
        $donnees = $repository->findWithoutDelete($user);
        
        $favs = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            1 // Nombre de résultats par page
        );
        return $this->render('user_dashboard/fav/favs.html.twig', [
            'favs' => $favs
        ]);
    }

    #[Route('/ajout-favori', name: 'add_fav')]
    public function add(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {

        $data = $request->query;
        if ($data->get('good_id') != null) {

            $good = $manager->getRepository(Good::class)->find($data->get('good_id'));
            $user = $this->security->getUser();

            $fav = new Fav();

            $fav->setUser($user);
            $fav->setGood($good);

            $manager->persist($fav);
            $manager->flush();
            $response = $serializer->serialize($fav, 'json', ['groups' => 'main']);

            return JsonResponse::fromJsonString($response);
        }
    }

    #[Route('/supprimer-favori', name: 'delete_fav')]
    public function remove(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {

        $data = $request->query;
        if ($data->get('fav_id') != null) {

            $fav = $manager->getRepository(Fav::class)->find($data->get('fav_id'));

            $manager->remove($fav);
            $manager->flush();
            $response = $serializer->serialize('Suppression effectuée', 'json');

            return JsonResponse::fromJsonString($response);
        }
    }

    #[Route('/retirer-favori/{id<\d+>}', name: 'remove_fav')]
    public function delete(Fav $fav, EntityManagerInterface $manager):Response
    {
        if ($fav) {
            $manager->remove($fav);
            $manager->flush();
            $this->addFlash('success', 'Offre retirée des favoris!');
            return $this->redirectToRoute('favs');
        }
    }
}
