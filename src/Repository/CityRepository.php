<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 *
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function save(City $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(City $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return City[] Returns an array of not deleted City objects only
    */
    public function findWithoutDelete(): array
    {
       return $this->createQueryBuilder('c')->where('c.deletedAt is NULL')->getQuery()->getResult();
    }

     /**
    * @return Cities[] Returns an array of not deleted cities related to one department object only
    */
    public function getCities($value): array
    {
       return $this->createQueryBuilder('c')
           ->where('c.deletedAt is NULL')
           ->andWhere('c.department = :val')
           ->setParameter('val', $value)
           ->getQuery()->getResult();
    }

}
