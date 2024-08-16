<?php

declare(strict_types=1);

namespace App\Dto;

// use 
use Symfony\Component\Validator\Constraints as Assert;

use App\Dto\OrderAmountDto;
class OrderLinesDto {

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $sku,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Url]
        public string $productUrl,

        #[Assert\NotBlank]
        #[Assert\Url]
        public string $imageUrl,

        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        public int $quantity,

        #[Assert\NotBlank]
        #[Assert\Type('numeric')]
        public $vatRate,

        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAmountDto $unitPrice,

        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAmountDto $totalAmount,

        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAmountDto $discountAmount,

        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAmountDto $vatRateAmount,
    ){}
}