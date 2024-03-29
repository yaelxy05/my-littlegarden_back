<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Legume;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Legume|null find($id, $lockMode = null, $lockVersion = null)
 * @method Legume|null findOneBy(array $criteria, array $orderBy = null)
 * @method Legume[]    findAll()
 * @method Legume[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegumeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Legume::class);
    }
    public function findLegumeForOneUser(User $user)
    {
        $user = $user->getId();
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Legume a
            WHERE a.user = ' . $user
        );
 
        return $query->getResult();
    }
    // /**
    //  * @return Legume[] Returns an array of Legume objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Legume
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
