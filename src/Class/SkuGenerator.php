<?php

namespace App\Class;

use App\Enum\CategoryTypeEnum;
use App\Enum\SubscriptionTypeEnum;

class SkuGenerator
{

    private $catRepo;
    private $subTypeRepo;
    public function __construct(
        // private ManagerRegistry $em
    )
    {
        // $this->categoryRepo = new CategoryRepository($this->em);
        // $this->subTypeRepo = new SubscriptionTypeRepository($this->em);
    }

    public function generateSku(string $cat, string $duration, $seq)
    {
        $skuStart = '';
        $skuMid = '';
        $skuEnd = '';
        // $cat = CategoryTypeEnum::toInt($cat);
        // /** @var CategoryTypeEnum $cat Object */
        // $cat = $cat;

        // /** @var SubscriptionTypeEnum $duration Object */
        // $duration = SubscriptionTypeEnum::toString($duration);
        // $duration = $duration;
        /** $changeDuration   */
        $changeDuration = '';

        if($duration == 'TWO_WEEKS')
        {
            $changeDuration = 'week';
        } else {
            $changeDuration = $duration;
        }

        $sequenceLength = strlen($seq);

        // $catSubName = $cat->name;
        // $catSubName = substr($cat->getId(), 0, 3);
        // $skuStart = strtoupper($catSubName);
        $skuStart = $cat;

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