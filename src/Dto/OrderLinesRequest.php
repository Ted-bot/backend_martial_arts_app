<?php

declare(strict_types=1);

namespace App\Dto;

// use 
use App\Dto\OrderAmountDto;

use App\Request\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

class OrderLinesRequest extends AbstractJsonRequest
{

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $sku;

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $name;

        #[Assert\NotBlank]
        #[Assert\Url]
        public string $productUrl;

        #[Assert\NotBlank]
        #[Assert\Url]
        public string $imageUrl;

        #[Assert\NotBlank]
        #[Assert\Type('int')]
        public int $quantity;

        #[Assert\NotBlank]
        #[Assert\Type('numeric')]
        public $vatRate;

        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAmountDto $unitPrice;

        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAmountDto $totalAmount;

        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAmountDto $discountAmount;

        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAmountDto $vatRateAmount;

}