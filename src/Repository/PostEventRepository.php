<?php

namespace App\Repository;

use App\Entity\PostEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface as EntityManager;

/**
 * @extends ServiceEntityRepository<PostEvent>
 */
class PostEventRepository extends ServiceEntityRepository
{
    public EntityManager $em;
    public function __construct(ManagerRegistry $registry, EntityManager $em)
    {
        parent::__construct($registry, PostEvent::class);
        $this->em = $em;
    }

    public function findAllPostEventsById(): array
    {
        return $this->em->createQueryBuilder()
        ->select('postev.id')
        ->from('App\Entity\PostEvent', 'postev')
        ->getQuery()
        ->getResult();
    }

    public function countSubscribtionsByEventId($id): array
    {
        return $this->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->where('p.id = :id')
        ->leftJoin('p.subscribe','subscribe')
        ->groupBy('p.id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
    }

    public function findSubscribtionIdsByEventId($id): array
    {
        return $this->createQueryBuilder('p')
        ->select('p.id')
        ->where('subscribe.id = :id')
        ->leftJoin('p.subscribe','subscribe')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
    }

    public function findAllQuery(
        bool $withSubscribtions = false
    ): QueryBuilder
    {   $query = $this->createQueryBuilder('p');

        if($withSubscribtions){
            $query->leftJoin('p.subscribe','subcribtions')
            ->addSelect('subscribtions');
        }

        return $query;
    }

    //    /**
    //     * @return PostEvent[] Returns an array of PostEvent objects
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

    //    public function findOneBySomeField($value): ?PostEvent
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
