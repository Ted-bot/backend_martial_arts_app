<?php

declare(strict_types=1);

namespace App\Dto;

use App\Request\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

class OrderAmountRequest extends AbstractJsonRequest
{

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('numeric')]
        public $value,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public $currency,
    ){}

}