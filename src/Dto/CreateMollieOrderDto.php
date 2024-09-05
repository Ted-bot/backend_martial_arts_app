<?php
declare(strict_types=1);

namespace App\Dto;

use App\Dto\OrderAmountDto;
use App\Dto\OrderAddressDto;
use App\Dto\OrderMetaDataDto;
use Mollie\Api\Types\SequenceType;
use Symfony\Component\Validator\Constraints as Assert;

class CreateMollieOrderDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $description,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            max: 15,
            maxMessage: 'Your order number cannot be longer than {{ limit }} characters',
        )]
        public string $order_id,

        #[Assert\NotBlank]
        #[Assert\Valid]
        public OrderAmountDto $amount,
        
        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAddressDto $billingAddress,
        
        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderAddressDto $shippingAddress,
        
        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderMetaDataDto $metadata,
        
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $locale,        

        #[Assert\NotBlank]
        #[Assert\Url]
        public string $redirectUrl,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $webhookUrl,
        
        public SequenceType|string $sequenceType,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $method,

        #[Assert\NotBlank]
        #[Assert\Valid]
        public array $lines,
        
        #[Assert\Valid]
        public OrderSubscriptionDto $subscriptionDetail,
    )
    {}
}