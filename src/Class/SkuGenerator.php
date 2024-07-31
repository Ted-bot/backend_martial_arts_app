<?php

namespace App\Class;

use App\Repository\CategoryRepository;
use App\Repository\SubscriptionTypeRepository;
use Doctrine\Persistence\ManagerRegistry;
class SkuGenerator
{

    private $categoryRepo;
    private $subTypeRepo;
    public function __construct(
        private ManagerRegistry $em
    )
    {
        $this->categoryRepo = new CategoryRepository($this->em);
        $this->subTypeRepo = new SubscriptionTypeRepository($this->em);
    }

    public function generateSku($cat, $duration, $seq)
    {
        $skuStart = '';
        $skuMid = '';
        $skuEnd = '';

        // $categoryRepo = new CategoryRepository();
        // $subTypeRepo = new SubscriptionTypeRepository();

        $category = $this->categoryRepo->findOneBy(['id' => $cat]);
        $duration = $this->subTypeRepo->findOneBy(['id' => $duration]);

        $changeDuration = '';

        if($duration->getDuration() == 'two weeks')
        {
            $changeDuration = 'week';
        } else {
            $changeDuration = $duration->getDuration();
        }

        $sequenceLength = strlen($seq);

        $catSubName = substr($category->getName(), 0, 3);
        $skuStart = strtoupper($catSubName);

        $firstCharacter = substr($changeDuration, 0, 1); // week
        $skuMid = strtoupper($firstCharacter);

        $addition = '';

        if($sequenceLength < 3){
            $toAdd = 3 - $sequenceLength;

            for($i = 0; $i < $toAdd; $i++){
                $addition .= '0';
            }

            $skuEnd .= $addition . $seq;

        }

        return  $skuStart . $skuMid . '1' . $skuEnd;
    }
}