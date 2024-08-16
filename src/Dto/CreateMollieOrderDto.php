<?php
declare(strict_types=1);

namespace App\Dto;

use App\Dto\OrderAmountDto;
use App\Dto\OrderAddressDto;
use App\Dto\OrderMetaDataDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateMollieOrderDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $description,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            max: 15,
            maxMessage: 'Your order number cannot be longer than {{ limit }} characters',
        )]
        public readonly string $order_id,

        #[Assert\NotBlank]
        #[Assert\Valid]
        public readonly ?OrderAmountDto $amount,
        
        #[Assert\NotBlank]
        #[Assert\Valid]
        public readonly ?OrderAddressDto $billingAddress,
        
        #[Assert\NotBlank]
        #[Assert\Valid]
        public readonly ?OrderAddressDto $shippingAddress,
        
        #[Assert\NotBlank]
        #[Assert\Valid]
        public readonly ?OrderMetaDataDto $metadata,
        
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $locale,        

        #[Assert\NotBlank]
        #[Assert\Url]
        public readonly string $redirectUrl,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $webhookUrl,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $method,

        #[Assert\NotBlank]
        #[Assert\Valid]
        public readonly array $lines,
    )
    {}
}