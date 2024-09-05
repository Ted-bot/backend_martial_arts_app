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
use Symfony\Component\Serializer\Attribute\Groups;
// use Symfony\Component\Serializer\Annotation\Groups;


#[
  ApiResource(normalizationContext: ['groups' => ['read']]),
  GetCollection(provider: Status::class.'::getCases'), 
  Get(provider: Status::class.'::getCase'),
]
enum SubscriptionTypeEnum: string
{
  use EnumApiResourceTrait;
  
    #[EnumCase('week')]
    case WEEK = 'week';
    
    #[EnumCase('month')]
    case MONTH = 'month';
    
    #[EnumCase('unavailable')]
    case UNAVAILABLE = 'unavailable';

    public static function toString(?self $value)
    {
      return match($value) {
          self::WEEK => 'week',
          self::MONTH => 'month',
          self::UNAVAILABLE => 'unavailable'
      };
    }
}