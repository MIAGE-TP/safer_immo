<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferTypeController extends AbstractController
{
    #[Route('/offer/type', name: 'app_offer_type')]
    public function index(): Response
    {
        return $this->render('offer_type/index.html.twig', [
            'controller_name' => 'OfferTypeController',
        ]);
    }
}
