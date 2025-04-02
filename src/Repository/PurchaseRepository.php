<?php

namespace App\Repository;

use App\Entity\ProductInfos;
use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Purchase>
 *
 * @method Purchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purchase[]    findAll()
 * @method Purchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

   /**
    * @return Purchase[] Returns an array of Purchase objects
    */
//    public function findByType($value): array
//    {
//        return $this->createQueryBuilder('p')
//     //    ->select('p', 'i')
//     //    ->leftJoin('App\Entity\ProductInfos', 'i')
//        ->leftJoin('p.product', 'i')
//            ->andWhere('p.product = i.id')
//            ->andWhere('p.type = :value')
//            ->setParameter('value', $value)
//            ->orderBy('i.cooldown', 'ASC')
//            ->getQuery()
//            ->getResult()
//        ;
//    }

   /**
    * @return Purchase[] Returns an array of Purchase objects
    */
    public function findByUserNull($product, $user = null): array
    {
        if($product){

            return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u') // LEFT JOIN pour inclure les NULL
            ->where('p.user IS NULL') // Filtrer uniquement ceux sans produit
            // ->addSelect('prod')
            ->orderBy('p.cooldown', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        } else {
            return $this->createQueryBuilder('p')
                ->innerJoin('p.user', 'u')
                ->where('p.user = :value')
                ->setParameter('value', $user)
                ->orderBy('p.cooldown', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }
    }

//    public function findOneBySomeField($value): ?Purchase
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
