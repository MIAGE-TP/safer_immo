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
use Carbon\Carbon;
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

        $location = $department->getName().', '.$city->getName().' ('.$city->getZipCode().'), '.$data->get('street');

        $good->setLocalisation($location);

        $good->setSurface($data->get('surface'));

        $good->setUnit($data->get('unit'));
        $good->setStreet($data->get('street'));

        $good->setPrice($data->get('price'));
        
        $good->setUser($currentUser);

        $good->setOffertype($offerType);
        $good->setGoodcategory($category);
        $good->setCity($city);

        $slug = $slugger->slug($data->get('intitule').'-'.uniqid());
        $good->setSlug($slug);
        
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

    public function update($request, $manager, $slugger, $dir)
    {
        $data = $request->request;

        $good = $manager->getRepository(Good::class)->find($data->get('id'));

        $good->setReference($data->get('reference'));
        $good->setIntitule($data->get('intitule'));
        $good->setDescriptif($data->get('description'));

        $department = $manager->getRepository(Department::class)->find($data->get('department'));
        $city = $manager->getRepository(City::class)->find($data->get('city'));
        $offerType = $manager->getRepository(OfferType::class)->find($data->get('offerType'));
        $category = $manager->getRepository(GoodCategory::class)->find($data->get('category'));
        $currentUser = $manager->getRepository(User::class)->find($data->get('user'));

        $location = $department->getName().', '.$city->getName().' ('.$city->getZipCode().'), '.$data->get('street');

        $good->setLocalisation($location);

        $good->setSurface($data->get('surface'));

        $good->setUnit($data->get('unit'));
        $good->setStreet($data->get('street'));

        $good->setPrice($data->get('price'));
        
        $good->setUser($currentUser);

        $good->setOffertype($offerType);
        $good->setGoodcategory($category);
        $good->setCity($city);

        $slug = $slugger->slug($data->get('intitule').'-'.uniqid());
        $good->setSlug($slug);
        $good->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
        $this->save($good, true);

        if ($data->all('deleted')[0] != null) {
            $filestoDelete = explode(',', $data->all('deleted')[0]);
            foreach ($filestoDelete as $fileToDel) {
                $deleted = $manager->getRepository(OfferGalery::class)->find($fileToDel);
                $manager->remove($deleted);
                $manager->flush();
            }
        }

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
           ->orderBy('g.id', 'DESC')
           ->getQuery()
           ->getResult();
    }

    /**
    * @return Good[] Returns an array of not deleted Good objects only
    */
    public function findAllForHome(): array
    {
       return $this->createQueryBuilder('g')
           ->where('g.deletedAt is NULL')
           ->orderBy('g.id', 'DESC')
           ->setMaxResults(3)
           ->getQuery()
           ->getResult();
    }

}
