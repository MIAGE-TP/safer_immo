<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function index()
    {
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    public function profile()
    {
        return $this->render('admin_dashboard/profile.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    public function good_types()
    {
        return $this->render('admin_dashboard/good_types.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
