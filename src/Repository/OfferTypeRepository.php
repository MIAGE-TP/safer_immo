<?php

namespace App\Repository;

use App\Entity\OfferType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OfferType>
 *
 * @method OfferType|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfferType|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfferType[]    findAll()
 * @method OfferType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfferType::class);
    }

    public function save(OfferType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OfferType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return OfferType[] Returns an array of not deleted OfferType objects only
    */
    public function findWithoutDelete(): array
    {
       return $this->createQueryBuilder('o')->where('o.deletedAt is NULL')->getQuery()->getResult();
    }
}
