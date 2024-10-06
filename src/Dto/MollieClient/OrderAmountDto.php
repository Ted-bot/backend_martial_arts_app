<?php

declare(strict_types=1);

namespace App\Dto\MollieClient;

use Symfony\Component\Validator\Constraints as Assert;

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