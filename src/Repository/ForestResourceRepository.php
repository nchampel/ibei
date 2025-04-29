<?php

namespace App\Repository;

use App\Entity\ForestResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForestResource>
 *
 * @method ForestResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForestResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForestResource[]    findAll()
 * @method ForestResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForestResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForestResource::class);
    }

    /**
    * @return ForestResource[] Returns an array of ForestResource objects
    */
    public function findByUserNull($forestResource, $user = null): array
    {
        if($forestResource){

            return $this->createQueryBuilder('f')
            ->leftJoin('f.user', 'u') // LEFT JOIN pour inclure les NULL
            ->where('f.user IS NULL') // Filtrer uniquement ceux sans produit
            // ->addSelect('prod')
            // ->orderBy('p.cooldown', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        } else {
            return $this->createQueryBuilder('f')
                ->innerJoin('f.user', 'u')
                ->where('f.user = :value')
                ->setParameter('value', $user)
                // ->orderBy('p.cooldown', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }
    }
    /**
    * @return ForestResource[] Returns an array of ForestResource objects
    */
    public function findDisplayable(){
        return $this->createQueryBuilder('f')
        ->join('f.forestResource', 'i')
        ->where('i.isDisplayed = true')
        // ->setParameter('displayed', true)
        ->getQuery()
        ->getResult();
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
