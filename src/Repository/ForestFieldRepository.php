<?php

namespace App\Repository;

use App\Entity\ForestField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForestField>
 *
 * @method ForestField|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForestField|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForestField[]    findAll()
 * @method ForestField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForestFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForestField::class);
    }

//    /**
//     * @return ForestField[] Returns an array of ForestField objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ForestField
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
