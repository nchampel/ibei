<?php

namespace App\Repository;

use App\Entity\Pot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pot>
 *
 * @method Pot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pot[]    findAll()
 * @method Pot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pot::class);
    }

//    /**
//     * @return Pot[] Returns an array of Pot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pot
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
