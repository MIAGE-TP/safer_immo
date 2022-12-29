<?php

namespace App\Repository;

use App\Entity\OfferGalery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OfferGalery>
 *
 * @method OfferGalery|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfferGalery|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfferGalery[]    findAll()
 * @method OfferGalery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferGaleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfferGalery::class);
    }

    public function save(OfferGalery $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OfferGalery $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
