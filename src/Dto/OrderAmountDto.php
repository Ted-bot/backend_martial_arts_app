<?php

declare(strict_types=1);

namespace App\Dto;

// use 
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

class OrderAmountDto {

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('numeric')]
        // #[JMS\Type('numeric')]
        // #[JMS\SerializedName('value')]
        // #[Assert\Type('numeric')]
        public $value,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        // #[JMS\Type('string')]
        // #[JMS\SerializedName('currency')]
        public string $currency,
    ){}
}