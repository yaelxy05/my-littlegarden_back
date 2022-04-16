<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Potager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function findPotagerForOneUser(User $user)
    {
        $user = $user->getId();
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Potager a
            WHERE a.user = ' . $user
        );

        return $query->getResult();
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
