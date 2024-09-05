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
enum CurrencyTypeEnum: string
{
  use EnumApiResourceTrait;
    
    #[EnumCase('eur')]
    case EUR = 'EUR';

}