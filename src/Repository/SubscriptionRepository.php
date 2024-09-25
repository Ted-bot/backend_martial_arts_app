<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findByIdThenReturnArray($id){
        return $this->createQueryBuilder('p')
        //  "SELECT date_start,date_end FROM subscription sub WHERE sub.subOwnedBy = :id ORDER BY createdAt DESC"
        // ->select('p.id')
        ->select('p.dateStart')
        ->addSelect('p.dateEnd')
        ->addSelect('p.duration')
        ->where('p.subOwnedBy = :id')
        ->where('p.status = paid')
        // ->leftJoin('p.subscribe','subscribe') // subscribe is relation table UserProfile
        ->setParameter('id', $id)
        ->orderBy('p.createdAt','DESC')
        ->getQuery()
        ->getResult();
    }
    //    /**
    //     * @return Subscription[] Returns an array of Subscription objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Subscription
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
