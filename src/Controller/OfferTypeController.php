<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\OfferType;
use Carbon\Carbon;
use App\Repository\OfferTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class OfferTypeController extends AbstractController
{
    #[Route('/admin/offer_types', name: 'offer_types')]
    public function index(OfferTypeRepository $repository): Response
    {
        $offerTypes = $repository->findWithoutDelete();
        return $this->render('admin_dashboard/offer_type/offer_types.html.twig', [
            'offer_types' => $offerTypes
        ]);
    }

    #[Route('/admin/ajout-offer_type', name: 'add_offer_type')]
    public function new(): Response
    {
        return $this->render('admin_dashboard/offer_type/add.html.twig', [
            'controller_name' => 'offer_typeController',
        ]);
    }

    #[Route('/admin/store-offer_type', name: 'store_offer_type', methods:['GET','POST'])]
    public function store(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;
        $offerType = new OfferType();

        $offerType->setLibelle($req->get('libelle'));

        $manager->persist($offerType);
        $manager->flush();

        return $this->redirectToRoute('offer_types');
    }
    
    #[Route('/admin/modification-offer_type/{id<\d+>}', name: 'edit_offer_type')]
    public function edit(int $id, OfferTypeRepository $repository): Response
    {
        $offerType = $repository->find($id);

        if ($offerType) {
            return  $this->render('admin_dashboard/offer_type/edit.html.twig', [
                'offer_type' => $offerType
            ]);
        }else {
            throw $this->createNotFoundException(
                "Aucun type d'offre ne correspond à l'id ".$id
            );
        }
    }

    #[Route('/admin/update-offer_type/', name: 'update_offer_type')]
    public function update(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;

        $offerType = $manager->getRepository(OfferType::class)->find($req->get('id'));

        if ($offerType) {

            $offerType->setLibelle($req->get('libelle'));
            $offerType->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $manager->flush();
            return $this->redirectToRoute('offer_types');

        }else {
            throw $this->createNotFoundException(
                "Erreur lors de la modification, merci de réessayer."
            );
        }
    }

    #[Route('/admin/suppression-offer_type/{id<\d+>}', name: 'delete_offer_type')]
    public function delete(int $id, EntityManagerInterface $manager): Response
    {
        $offerType = $manager->getRepository(OfferType::class)->find($id);
        if ($offerType) {
            if (($offerType->getGoods()->count()) > 0) {
                throw $this->createNotFoundException(
                    "Supression impossible, ce type d'offre est lié à plusieurs villes."
                );
            }else {
                $offerType->setDeletedAt(Carbon::now()->toDateTimeImmutable());
                $manager->flush();
                return $this->redirectToRoute('offer_types');
            }
        }
    }
}
