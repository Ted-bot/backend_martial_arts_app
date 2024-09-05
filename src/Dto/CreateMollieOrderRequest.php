<?php
declare(strict_types=1);

namespace App\Dto;

use App\Dto\OrderAmountRequest;
use App\Dto\OrderAddressRequest;
use App\Dto\OrderMetaDataRequest;
use Mollie\Api\Types\SequenceType;
use App\Request\AbstractJsonRequest;
use App\Dto\OrderSubscriptionRequest;
use Symfony\Component\Validator\Constraints as Assert;

class CreateMollieOrderRequest extends AbstractJsonRequest
{
    // public function __construct;
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $description;

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            max: 15,
            maxMessage: 'Your order number cannot be longer than {{ limit }} characters',
        )]
        public string $order_id;

        // #[Assert\NotBlank]
        #[Assert\Valid]
        public OrderAmountRequest $amount;
        
        // #[Assert\NotBlank]
        #[Assert\Valid]
        public OrderAddressRequest $billingAddress;
        
        #[Assert\NotBlank]
        #[Assert\Valid]
        public OrderAddressRequest $shippingAddress;
        
        #[Assert\NotBlank]
        #[Assert\Valid]
        public ?OrderMetaDataRequest $metadata;
        
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $locale;

        #[Assert\NotBlank]
        #[Assert\Url]
        public string $redirectUrl;

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $webhookUrl;
        
        public SequenceType|string $sequenceType;

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $method;

        #[Assert\NotBlank]
        #[Assert\Valid]
        public array $lines;
        
        #[Assert\Valid]
        public OrderSubscriptionRequest $subscriptionDetail;
}