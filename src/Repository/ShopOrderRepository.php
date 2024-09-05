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
      //   $testQb = $this->createQueryBuilder("so")
      //           ->join('so.orderLines','orderLines')
      //           ->join('orderLines.product','product')
      //           ->join('product.productVats','prTax')
      //           ->addSelect('product.price * orderLines.qty as totalPriceExlAmount')
      //           ->where('so.ownedBy = :user')
      //           ->andWhere('orderLines.shopOrder = so.id')
      //           ->andWhere("so.orderStatus != 3") // status = paid
      //           ->setParameter('user', $user)
      //           // ->setParameter('calcPrices',  $testQb)
      //           ->orderBy('so.orderDate','DESC')
      //          ->getQuery()
      //          ->getResult();

      //       $testQbSec = $this->createQueryBuilder("so")
      //          ->join('so.orderLines','orderLines')
      //          ->join('orderLines.product','product')
      //          ->join('product.productVats','prTax')
      //          ->addSelect('prTax.VatAmount * orderLines.qty as totalProdTaxAmount')
      //          ->where('so.ownedBy = :user')
      //          ->andWhere('orderLines.shopOrder = so.id')
      //          ->andWhere("so.orderStatus != 3") // status = paid
      //          ->setParameter('user', $user)
      //          // ->setParameter('calcPrices',  $testQb)
      //          ->orderBy('so.orderDate','DESC')
      //         ->getQuery()
      //         ->getResult();
            $qb = $this->createQueryBuilder('so');

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
               //  ->addSelect($qb->expr()->sum('product.name','product.name'))
                ->addSelect('taxRate.procent as taxPercent')
                ->join('so.orderLines','orderLines')
                ->join('orderLines.product','product')
                ->join('product.productVats','prTax')
                ->join('prTax.VatRate','taxRate')
                ->where('so.ownedBy = :user')
                ->andWhere('orderLines.shopOrder = so.id')
               //  ->andWhere("so.orderStatus != paid") // status = paid / 3
                ->setParameter('user', $user)
                // ->setParameter('calcPrices',  $testQb)
               ->orderBy('so.orderDate','DESC')
               ->getQuery()
            //    ->getSingleResult()
            // ->getSingleScalarResult()
               ->getResult()
            //    ->getOneOrNullResult()
         //   ,
      //   'test' => $testQb[0]['totalPriceExlAmount'] + $testQbSec[0]['totalProdTaxAmount']]
        ;
       }
}
