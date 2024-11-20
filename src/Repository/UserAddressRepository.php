<?php

namespace App\Repository;

use App\Entity\UserAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<UserAddress>
 */
class UserAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAddress::class);
    }

    public static function criteriaUserAddress($operator = 'lt')
    {
        return Criteria::create()
            ->andWhere((Criteria::expr()->eq('isDefault', true)))
            ;
    }
    public function findUserDefaultAddress($id)
    {
        return $this->createQueryBuilder('ua')
            ->select('ua.id AS userAddressId')
            ->addSelect('address.id AS AddressId')
            ->addSelect('address.unitNumber')
            ->addSelect('address.streetNumber')
            ->addSelect('address.addressLine')
            ->addSelect('address.postalCode')
            ->addSelect('address.libReactCity AS cityId')
            ->addSelect('address.city AS city')
            ->addSelect('address.libReactState AS stateId')
            // ->addSelect('user.libReactCity')
            // ->addSelect('user.libReactState')
            ->leftJoin('ua.address', 'address')
            ->leftJoin('ua.addressUser', 'user')
            ->andWhere('address.id = ua.address')
            ->addCriteria(self::criteriaUserAddress())
            ->andWhere('ua.addressUser = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return UserAddress[] Returns an array of UserAddress objects
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

    //    public function findOneBySomeField($value): ?UserAddress
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
