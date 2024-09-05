<?php

declare(strict_types=1);

namespace App\Dto;

// use 
use Symfony\Component\Validator\Constraints as Assert;

class OrderMetaDataDto {

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $order_id,
    ){}
}

