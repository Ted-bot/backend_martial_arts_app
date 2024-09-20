<?php

declare(strict_types=1);

namespace App\Dto;

// use 
use App\Request\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

class OrderSubscriptionRequest extends AbstractJsonRequest 
{

    // public function __construct(
        #[Assert\NotBlank]
        public string $subscriptionTimeUnit;
        
        #[Assert\NotBlank]
        #[Assert\Date]
        public $productSubscriptionEnd;
        
        #[Assert\NotBlank]
        #[Assert\Date]
        public $productSubscriptionStart;
        
        #[Assert\NotBlank]
        public string $subscriptionAmount;
        
        #[Assert\NotBlank]
        public int $subscriptionLength;

}