<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Department;
use App\Entity\Good;
use App\Entity\GoodCategory;
use App\Entity\OfferGalery;
use App\Entity\OfferType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CityRepository;
use App\Repository\DepartmentRepository;
use App\Repository\OfferTypeRepository;
use App\Repository\GoodCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Length;

class GoodController extends AbstractController
{
    #[Route('/ajout-bien', name: 'add_good')]
    public function add(DepartmentRepository $departmentRepository, GoodCategoryRepository $catRepository,
                          CityRepository $cityRepository, OfferTypeRepository $offTypRepository
    ): Response
    {
        $categories = $catRepository->findWithoutDelete();
        $departments = $departmentRepository->findWithoutDelete();
        $cities = $cityRepository->findWithoutDelete();
        $offerTypes = $offTypRepository->findWithoutDelete();
        return $this->render('admin_dashboard/good/add.html.twig', [
            'categories' => $categories,
            'departments' => $departments,
            'cities' => $cities,
            'offerTypes' => $offerTypes
        ]);
    }

    #[Route('/store-bien', name: 'store_good')]
    public function store(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger):Response
    {
        $data = $request->request;
        $files = $request->files->get('files');
        $good = new Good();
        $good->setReference($data->get('reference'));
        $good->setIntitule($data->get('intitule'));
        $good->setDescriptif($data->get('description'));

        $department = $manager->getRepository(Department::class)->find($data->get('department'));
        $city = $manager->getRepository(City::class)->find($data->get('city'));
        $offerType = $manager->getRepository(OfferType::class)->find($data->get('offerType'));
        $category = $manager->getRepository(GoodCategory::class)->find($data->get('category'));
        $currentUser = $manager->getRepository(User::class)->find($data->get('user'));

        $location = $department->getName().' '.$city->getName().' '.$data->get('street');

        $good->setLocalisation($location);

        $good->setSurface($data->get('surface').' '.$data->get('unit'));

        $good->setPrice($data->get('price'));
        
        $good->setUser($currentUser);

        $good->setOffertype($offerType);
        $good->setGoodcategory($category);
        $good->setCity($city);
        $manager->persist($good);
        $manager->flush();

        if ($files) {
            foreach ($files as $file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('offer_galery'),
                        $newFilename
                    );
                    $galery = new OfferGalery();
                    $galery->setName($originalFilename);
                    $galery->setPath($newFilename);
                    $galery->setOffer($good);

                    $manager->persist($galery);
                    $manager->flush();
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
        }
        $this->addFlash('success', 'Offre immobilière ajoutée!');
        return $this->redirectToRoute('add_good');
    }
}
