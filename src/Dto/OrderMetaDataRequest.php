<?php

declare(strict_types=1);

namespace App\Dto;

// use 
use App\Request\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

class OrderMetaDataRequest extends AbstractJsonRequest
{
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $order_id;
}

