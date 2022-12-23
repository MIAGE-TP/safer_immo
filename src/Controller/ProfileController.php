<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/admin/profil', name: 'profile')]
    public function index(): Response
    {
        return $this->render('admin_dashboard/profile/profile.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
