<?php

namespace App\Repository;

use App\Entity\Potager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Potager|null find($id, $lockMode = null, $lockVersion = null)
 * @method Potager|null findOneBy(array $criteria, array $orderBy = null)
 * @method Potager[]    findAll()
 * @method Potager[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PotagerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Potager::class);
    }

    // /**
    //  * @return Potager[] Returns an array of Potager objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Potager
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
