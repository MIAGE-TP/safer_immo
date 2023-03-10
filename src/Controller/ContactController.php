<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GoodCategory;
use App\Entity\OfferType;
use App\Entity\City;
use App\Entity\Contact;
use App\Entity\Department;
use App\Repository\ContactRepository;

class ContactController extends AbstractController
{
    /*
    load categories, cities, offerTypes, departments as the multi crtiteria form displayed on contact page
    use them
    */
    #[Route('/contact', name: 'contact-us')]
    public function contact(EntityManagerInterface $manager)
    {
        $categories =  $manager->getRepository(GoodCategory::class)->findWithoutDelete();
        $cities = $manager->getRepository(City::class)->findWithoutDelete();
        $offerTypes = $manager->getRepository(OfferType::class)->findWithoutDelete();
        $departments = $manager->getRepository(Department::class)->findWithoutDelete();

        return $this->render('public/contact.html.twig', [
            'categories' => $categories,
            'cities' => $cities,
            'offerTypes' => $offerTypes,
            'departments' => $departments,
        ]);
    }

    /*
    save a message sent from the contact page
    */
    #[Route('/store-contact', name: 'store_contact')]
    public function store(Request $request, EntityManagerInterface $manager):Response
    {
        $data = $request->request;
        $contact = new Contact();

        $contact->setEmail($data->get('email'));
        $contact->setName($data->get('name'));
        $contact->setSubject($data->get('subject'));

        $category = $manager->getRepository(GoodCategory::class)->find($data->get('category'));

        $contact->setCategory($category);
        $contact->setMessage($data->get('message'));

        $manager->persist($contact);
        $manager->flush();
        
        $this->addFlash('success', 'Message envoy??!');
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }

    /*
    display all messages received from contact page
    */
    #[Route('/admin/contacts', name: 'contacts')]
    public function index(ContactRepository $repository): Response
    {
        $contacts = $repository->findAll();
        return $this->render('admin_dashboard/contact/contacts.html.twig', [
            'contacts' => $contacts
        ]);
    }

    /*
    display a specific  message and its details
    */
    #[Route('/admin/affichage-message/{id<\d+>}', name: 'display_message')]
    public function display(Contact $contact): Response
    {
        if ($contact) {
            return $this->render('admin_dashboard/contact/display.html.twig', [
                'contact' => $contact
            ]);
        }
    }

    /*
    destroy a specific  message and its details
    */
    #[Route('/admin/suppression-message/{id<\d+>}', name: 'delete_message')]
    public function delete(Contact $contact, EntityManagerInterface $manager): Response
    {
        if ($contact) {
            $manager->remove($contact);
            $manager->flush();
            $this->addFlash('success', 'Message supprim??!');
            return $this->redirectToRoute('contacts');
        }
    }
}
