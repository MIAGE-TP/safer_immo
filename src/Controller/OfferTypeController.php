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
    #[Route('/admin/types-offre', name: 'offer_types')]
    public function index(OfferTypeRepository $repository): Response
    {
        $offerTypes = $repository->findWithoutDelete();
        return $this->render('admin_dashboard/offer_type/offer_types.html.twig', [
            'offer_types' => $offerTypes
        ]);
    }

    #[Route('/admin/ajout-type-offre', name: 'add_offer_type')]
    public function new(): Response
    {
        return $this->render('admin_dashboard/offer_type/add.html.twig', [
            'controller_name' => 'OfferTypeController',
        ]);
    }

    #[Route('/admin/store-type-offre', name: 'store_offer_type', methods:['GET','POST'])]
    public function store(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;
        $offerType = new OfferType();

        $offerType->setLibelle($req->get('libelle'));

        $manager->persist($offerType);
        $manager->flush();
        $this->addFlash('success', 'Type d\'offre bien ajouté!');
        return $this->redirectToRoute('offer_types');
    }
    
    #[Route('/admin/modification-type-offre/{id<\d+>}', name: 'edit_offer_type')]
    public function edit(OfferType $offerType): Response
    {
        if ($offerType) {
            return  $this->render('admin_dashboard/offer_type/edit.html.twig', [
                'offer_type' => $offerType
            ]);
        }else {
            throw $this->createNotFoundException(
                "Aucun type d'offre ne correspond à l'id ".$offerType->getId()
            );
        }
    }

    #[Route('/admin/update-type_offre/', name: 'update_offer_type')]
    public function update(Request $request, EntityManagerInterface $manager): Response
    {
        $req = $request->request;

        $offerType = $manager->getRepository(OfferType::class)->find($req->get('id'));

        if ($offerType) {
            $offerType->setLibelle($req->get('libelle'));
            $offerType->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
            $manager->flush();
            $this->addFlash('success', 'Modification bien enregistrée!');
            return $this->redirectToRoute('offer_types');

        }else {
            throw $this->createNotFoundException(
                "Erreur lors de la modification, merci de réessayer."
            );
        }
    }

    #[Route('/admin/suppression-type-offre/{id<\d+>}', name: 'delete_offer_type')]
    public function delete(OfferType $offerType, EntityManagerInterface $manager): Response
    {
        if ($offerType) {
            if (($offerType->getGoods()->count()) > 0) {
                throw $this->createNotFoundException(
                    "Supression impossible, ce type d'offre est lié à plusieurs villes."
                );
            }else {
                $offerType->setDeletedAt(Carbon::now()->toDateTimeImmutable());
                $manager->flush();
                $this->addFlash('success', 'Suppresion effectuée!');
                return $this->redirectToRoute('offer_types');
            }
        }
    }
}
