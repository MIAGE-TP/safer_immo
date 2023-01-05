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

    /**
    * @return Favs[] Returns an array of all users Fav objects group by good only
    */
    public function UsersFav(): array
    {
       
        $query = $this->getEntityManager()->createQuery(
            'SELECT IDENTITY(f.good) as good_id
            FROM App\Entity\Fav f
            WHERE f.deletedAt is NULL
            GROUP BY f.good'
        );

        return $query->getResult();
    }
}
