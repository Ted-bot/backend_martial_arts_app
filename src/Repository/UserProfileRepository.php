<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\PostEvent;
use App\Entity\UserProfile;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<UserProfile>
 */
class UserProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProfile::class);
    }

    public function upgradePassword(User $user, string $newHashedPassword): void
    {
        // set the new hashed password on the User object
        $user->setPassword($newHashedPassword);

        $this->getEntityManager()->persist($user);
        // execute the queries on the database
        $this->getEntityManager()->flush();
    }

    // public function findUserAgenda(
    //     int $userId
    // )
    // {   
    //     $query = $this->createQueryBuilder('up')
    //         ->select('up.id')
    //         // ->select('pe.id')
    //         // ->addSelect('pe.startDate')
    //         // ->addSelect('pe.endDate')
    //         // ->addSelect('pe.title')
    //         // ->addSelect('selectedEvent.id AS eventSelectedByUser')
    //         // ->addSelect("CASE
    //         //             WHEN pe.id = userSubscribedEvents.id THEN '1'
    //         //             ELSE '0'
    //         //         END AS subscribedToEvent")
    //         // ->join('up.subscribeToEvents','selectedEvents')
    //         // ->leftJoin(PostEvent::Class, 'pe', "WITH", 'pe.isPublished = true')
    //         // ->leftJoin(PostEvent::Class, 'pe', "WITH", 'pe.isPublished = true')
    //         // ->set()
    //         // ->leftJoin('pe.subscribe', 'userProfile', 'WITH', 'userProfile.userUniq = :id')
    //         // ->join('userProfile.subscribeToEvents', 'userSubscribedEvents', 'WITH', 'userSubscribedEvents.id = pe.id')
    //         ->where('up.userUniq = :id')
    //         ->setParameter('id', $userId)
    //         // ->setMaxResults(2)
    //         ->getQuery()
    //         ->getResult();

    //     return $query;
    // }
    
    // public function remove(UserProfile $entity, bool $flush = false): void
    // {
    //     $this->getEntityManager()->remove($entity);

    //     if ($flush) {
    //         $this->getEntityManager()->flush();
    //     }
    // }

    //    /**
    //     * @return UserProfile[] Returns an array of UserProfile objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserProfile
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
