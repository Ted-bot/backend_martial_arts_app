<?php

namespace App\Repository;

use DateTimeZone;
use DateTimeImmutable;
use App\Entity\PostEvent;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<PostEvent>
 */
class PostEventRepository extends ServiceEntityRepository
{
    // public EntityManager $em;
    public function __construct(ManagerRegistry $registry) //, EntityManager $em
    {
        parent::__construct($registry, PostEvent::class);
        // $this->em = $em;
    }

    public function findAllPostEventsById(): array
    {
        return $this->em->createQueryBuilder()
        ->select('postev.id')
        ->from('App\Entity\PostEvent', 'postev')
        ->getQuery()
        ->getResult();
    }

    public function countUserSubscribedToEventByUserId($id): array
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

    public function countSubscribtionsByEventId($id, $status): array
    {
        return $this->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->where('subscribe.userProfile = :id')
        // ->where('p.id = :id')
        ->where(['p.isPublished = :status'])
        ->leftJoin('p.subscribe','subscribe') // subscribe is relation table UserProfile
        ->groupBy('p.id')
        ->setParameter('id', $id)
        ->setParameter('status', $status)
        ->getQuery()
        ->getResult();
    }

    public function findSubscribtionIdsByEventId($id): array
    {
        return $this->createQueryBuilder('p')
        ->select('p.id')
        ->where('subscribe.id = :id')
        ->leftJoin('p.subscribe','subscribe') // subscribe is relation table UserProfile
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
    }

    
    // public function findUserAgenda(
    //     int $userId
    // )
    // {   
//     $query =  $this->createQueryBuilder('p')
//             ->select('p.id')
//             ->addSelect('p.startDate')
//             ->addSelect('p.endDate')
//             ->addSelect('p.title')
// ->andWhere('p.isPublished = true')
// ->getQuery()
//             ->getResult();
        // $test = $this->
        // $query = $this->createQueryBuilder('p')
        //     ->select('p.id')
        //     ->addSelect('p.startDate')
        //     ->addSelect('p.endDate')
        //     ->addSelect('p.title')
        //     ->addSelect("CASE
        //                 WHEN p.id = userSubscribedEvent.id THEN '1'
        //                 ELSE '0'
        //             END AS subscribedToEvent")
        //     ->join('p.subscribe','userProfile',"WITH", 'userProfile.userUniq = :id')
        //     ->join('userProfile.subscribeToEvents','userSubscribedEvent')
        //     ->andWhere('p.isPublished = true')
        //     ->setParameter('id', $userId)
        //     ->getQuery()
        //     ->getResult();

        // return $query;
    // }

    public static function findUserSubscribedPublishedEventPostEvents($operator = 'lt')
    {
        return Criteria::create()
            ->andWhere((Criteria::expr()->eq('isPublished', true)))
            ->andWhere((Criteria::expr()->$operator('startDate', (new DateTimeImmutable())
                ->setTimezone(new DateTimeZone('Europe/Amsterdam')))
            ))
            ;
    }

    public static function findUserSubscribedEventForCalendar($operator = 'lt')
    {
        return Criteria::create()
            ->andWhere((Criteria::expr()->eq('isPublished', true)))
            ;
    }

    public function findUserPreviousSubscribedAndPublishedEvents($id)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->leftJoin('p.subscribe', 'userProfile')
            ->where('userProfile.userUniq = :id')
            ->addCriteria(self::findUserSubscribedPublishedEventPostEvents())
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
    }

    public function getArrayPublishedAndUserSubscribedEventIds($id, $matchUserSelectedEventId)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->leftJoin('p.subscribe', 'userProfile')
            ->addCriteria(self::findUserSubscribedEventForCalendar())
            ->andWhere('userProfile.userUniq = :id')
            ->andWhere('p.id = :selectedEventId')
            ->setParameter('id',$id)
            ->setParameter('selectedEventId',$matchUserSelectedEventId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUserUpcomingSubscribedAndPublishedEvent($id)
    {
        return $this->createQueryBuilder('p')
            ->select('p.startDate')
            ->addSelect('p.endDate')
            ->leftJoin('p.subscribe', 'userProfile')
            ->where('userProfile.userUniq = :id')
            ->addCriteria(self::findUserSubscribedPublishedEventPostEvents('gt'))
            ->setParameter('id',$id)
            ->orderBy('p.startDate','ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
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
