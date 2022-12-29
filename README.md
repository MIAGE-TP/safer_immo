<!-- Friendly remember for a custom query builder using repository -->
/**
* @return Good[] Returns an array of Good objects
*/
public function findByExampleField($value): array
{
    return $this->createQueryBuilder('g')
        ->andWhere('g.exampleField = :val')
        ->setParameter('val', $value)
        ->orderBy('g.id', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
    ;
}

public function findOneBySomeField($value): ?Good
{
    return $this->createQueryBuilder('g')
        ->andWhere('g.exampleField = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getOneOrNullResult()
    ;
}

1- composer install
2- create a .env file
3- set in .env according to the given exmaple in .env.test file.
4- doctrine:database:create after seeting up the DATABASE_URL
5- run migration with doctrine:migrations:migrate
6- symfony serve

That's all ;).