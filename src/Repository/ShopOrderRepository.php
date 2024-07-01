<?php

namespace App\Repository;

use App\Entity\ShopOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShopOrder>
 */
class ShopOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopOrder::class);
    }

    //    /**
    //     * @return ShopOrder[] Returns an array of ShopOrder objects
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

       public function findUserLatestOrder($user): ?array
       {
           return $this->createQueryBuilder('so')
                ->select('so.id')
                // ->addSelect('so.totalAmount')
                ->addSelect('orderLines.qty')
                ->addSelect('product.name')
                ->addSelect('product.price')
                ->addSelect('prTax.id as prTaxId')
                ->addSelect('prTax.VatAmount as singleProdPriceTax')
                ->addSelect('prTax.VatAmount * orderLines.qty as totalProdTaxAmount')
                ->addSelect('product.price * orderLines.qty as totalPriceExlAmount')
                // ->addSelect('COALESCE(totalPriceExlAmount) * COALESCE(totalProdTaxAmount) as totalProdPriceAmount')
                ->addSelect('taxRate.procent as taxPercent')
                ->join('so.orderLines','orderLines')
                ->join('orderLines.product','product')
                ->join('product.productVats','prTax')
                ->join('prTax.VatRate','taxRate')
                ->where('so.ownedBy = :user')
                ->andWhere('orderLines.shopOrder = so.id')
                ->andWhere("so.orderStatus != 3") // status = paid
                ->setParameter('user', $user)
               ->orderBy('so.orderDate','DESC')
               ->getQuery()
            //    ->getSingleResult()
               ->getResult()
            //    ->getOneOrNullResult()
           ;
       }
}
