<?php

namespace App\Repository;

use App\Entity\Idle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Idle>
 *
 * @method Idle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Idle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Idle[]    findAll()
 * @method Idle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Idle::class);
    }

//    /**
//     * @return Idle[] Returns an array of Idle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Idle
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
