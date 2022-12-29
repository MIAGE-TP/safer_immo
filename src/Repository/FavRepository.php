<?php

namespace App\Repository;

use App\Entity\Fav;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fav>
 *
 * @method Fav|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fav|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fav[]    findAll()
 * @method Fav[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fav::class);
    }

    public function save(Fav $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Fav $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return Fav[] Returns an array of not deleted Fav objects only
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
}
