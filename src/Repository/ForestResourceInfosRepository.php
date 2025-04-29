<?php

namespace App\Repository;

use App\Entity\ForestResourceInfos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForestResource>
 *
 * @method ForestResourceInfos|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForestResourceInfos|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForestResourceInfos[]    findAll()
 * @method ForestResourceInfos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForestResourceInfosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForestResourceInfos::class);
    }

//    /**
//     * @return ForestResource[] Returns an array of ForestResource objects
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

//    public function findOneBySomeField($value): ?ForestResource
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
