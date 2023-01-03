<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profil', name: 'profile')]
    public function index(EntityManagerInterface $manager): Response
    {
        $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
        return $this->render('admin_dashboard/profile/profile.html.twig', [
            'controller_name' => 'ProfileController',
            'categories' => $categories
        ]);
    }
}
