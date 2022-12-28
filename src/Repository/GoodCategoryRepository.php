<?php

namespace App\Repository;

use App\Entity\GoodCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GoodCategory>
 *
 * @method GoodCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoodCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoodCategory[]    findAll()
 * @method GoodCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoodCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoodCategory::class);
    }

    public function save(GoodCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GoodCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return GoodCategory[] Returns an array of not deleted GoodCategory objects only
    */
    public function findWithoutDelete(): array
    {
       return $this->createQueryBuilder('g')->where('g.deletedAt is NULL')->getQuery()->getResult();
    }
}
