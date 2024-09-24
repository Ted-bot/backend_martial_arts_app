<?php

declare(strict_types=1);

namespace App\Enum;

// use App\Class\Trait\EnumToArray;
use ApiPlatform\Metadata\Get;
use Elao\Enum\Attribute\EnumCase;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\EnumApiResourceTrait;

#[
  ApiResource(normalizationContext: ['groups' => ['read']]),
  GetCollection(provider: Status::class.'::getCases'), 
  Get(provider: Status::class.'::getCase'),
]
enum MolliePaymentStatusEnum: string
{
  use EnumApiResourceTrait;
    // use SQLEnumTypeTrait;
    // use EnumToArray;
    
    #[EnumCase('paid')]
    case PAID = 'paid';
    
    #[EnumCase('refunding')]
    case REFUNDING = 'refunding';
    
    #[EnumCase('processing')]
    case PROCESSING = 'processing';
    
    #[EnumCase('cancelled')]
    case CANCELLED = 'cancelled';
    
    #[EnumCase('open')]
    case OPEN = 'open';
    
    #[EnumCase('pending')]
    case PENDING = 'pending';
    
    #[EnumCase('authorized')]
    case AUTHORIZED = 'authorized';
    
    #[EnumCase('expired')]
    case EXPIRED = 'expired';
    
    #[EnumCase('failed')]
    case FAILED = 'failed';
}