<?php

declare(strict_types=1);

namespace App\Enum;

// use App\Class\Trait\EnumToArray;
use ApiPlatform\Metadata\Get;
use App\Enum\CountryTypeEnum;
use Elao\Enum\Attribute\EnumCase;
// use App\DBAL\Types\SQLEnumTypeTrait;
use App\Enum\EnumApiResourceTrait;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
// use Symfony\Component\Serializer\Annotation\Groups;

#[
  ApiResource(normalizationContext: ['groups' => ['read']]),
  GetCollection(provider: Status::class.'::getCases'), 
  Get(provider: Status::class.'::getCase'),
]
enum CountryToCurrencyTypeEnum: string
{
    use EnumApiResourceTrait;
    
    #[EnumCase('NL')]
    case PayInEurope = 'EUR';


    public static function toString(?self $value)
    {
      return match($value) {
          self::PayInEurope => 'EUR',
      };
    }

    public static function getCurrencyType(CountryTypeEnum $country)
  {
    return match($country) {
      CountryTypeEnum::NL_CODE => 'EUR',
    };
  }
}