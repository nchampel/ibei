<?php

namespace App\Repository;

use App\Entity\ProductInfos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductInfos>
 *
 * @method ProductInfos|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductInfos|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductInfos[]    findAll()
 * @method ProductInfos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductInfosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductInfos::class);
    }

//    /**
//     * @return ProductInfos[] Returns an array of ProductInfos objects
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

//    public function findOneBySomeField($value): ?ProductInfos
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
