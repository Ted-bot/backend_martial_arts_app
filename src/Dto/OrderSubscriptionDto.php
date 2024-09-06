<?php

declare(strict_types=1);

namespace App\Dto;

// use 
use Symfony\Component\Validator\Constraints as Assert;

class OrderSubscriptionDto {

    public function __construct(
        #[Assert\NotBlank]
        public string $productSubscription,
        
        #[Assert\NotBlank]
        #[Assert\Date]
        public $productSubscriptionEnd,
        
        #[Assert\NotBlank]
        #[Assert\Date]
        public $productSubscriptionStart,
        
        #[Assert\NotBlank]
        public string $subscriptionAmount,
        
        #[Assert\NotBlank]
        public int $subscriptionLength,
      
    ){}
}