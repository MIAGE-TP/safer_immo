<?php

namespace App\Repository;

use App\Entity\Good;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OfferGalery;
use App\Entity\City;
use App\Entity\Department;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\GoodCategory;
use App\Entity\OfferType;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Good>
 *
 * @method Good|null find($id, $lockMode = null, $lockVersion = null)
 * @method Good|null findOneBy(array $criteria, array $orderBy = null)
 * @method Good[]    findAll()
 * @method Good[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Good::class);
    }

    public function save(Good $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Good $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function insert($request, $manager, $slugger, $dir)
    {
        $data = $request->request;

        $good = new Good();
        $good->setReference($data->get('reference'));
        $good->setIntitule($data->get('intitule'));
        $good->setDescriptif($data->get('description'));

        $department = $manager->getRepository(Department::class)->find($data->get('department'));
        $city = $manager->getRepository(City::class)->find($data->get('city'));
        $offerType = $manager->getRepository(OfferType::class)->find($data->get('offerType'));
        $category = $manager->getRepository(GoodCategory::class)->find($data->get('category'));
        $currentUser = $manager->getRepository(User::class)->find($data->get('user'));

        $location = $department->getName().', '.$city->getName().', '.$data->get('street');

        $good->setLocalisation($location);

        $good->setSurface($data->get('surface').' '.$data->get('unit'));

        $good->setPrice($data->get('price'));
        
        $good->setUser($currentUser);

        $good->setOffertype($offerType);
        $good->setGoodcategory($category);
        $good->setCity($city);
        
        $this->save($good, true);

        $files = $request->files->get('files');

        if ($files) {
            foreach ($files as $file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $dir,
                        $newFilename
                    );
                    $galery = new OfferGalery();
                    $galery->setName($originalFilename);
                    $galery->setPath($newFilename);
                    $galery->setGood($good);
                    $manager->persist($galery);
                    $manager->flush();
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
        }
    }

    /**
    * @return Good[] Returns an array of not deleted Good objects only
    */
    public function findWithoutDelete($value): array
    {
       return $this->createQueryBuilder('g')
           ->where('g.deletedAt is NULL')
           ->andWhere('g.user = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getResult();
    }
}
