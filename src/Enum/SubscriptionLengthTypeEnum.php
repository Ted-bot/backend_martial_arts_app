<?php

declare(strict_types=1);

namespace App\Enum;

// use App\Class\Trait\EnumToArray;
use ApiPlatform\Metadata\Get;
use Elao\Enum\Attribute\EnumCase;
use ApiPlatform\Metadata\Operation;
// use App\DBAL\Types\SQLEnumTypeTrait;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\EnumApiResourceTrait;
// use Symfony\Component\Serializer\Annotation\Groups;

#[
  ApiResource(normalizationContext: ['groups' => ['read']]),
  GetCollection(provider: Status::class.'::getCases'), 
  Get(provider: Status::class.'::getCase'),
]
enum SubscriptionLengthTypeEnum: int
{
    use EnumApiResourceTrait;
    
    #[EnumCase('unavailble')]
    case UNAVAILABLE = 0;
    
    #[EnumCase('period_times_two')]
    case PERIOD_TIMES_TWO = 2;
    
    #[EnumCase('months_four')]
    case MONTH_FOUR = 4;
    
    #[EnumCase('months_six')]
    case MONTH_SIX = 6;
    
    #[EnumCase('months_twelve')]
    case MONTH_TWELVE = 12;

}