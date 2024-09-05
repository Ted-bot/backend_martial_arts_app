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
use Symfony\Component\Serializer\Annotation\Groups;

#[
  ApiResource(normalizationContext: ['groups' => ['read']]),
  GetCollection(provider: Status::class.'::getCases'), 
  Get(provider: Status::class.'::getCase'),
]
enum CategoryTypeEnum: string
{
    use EnumApiResourceTrait;
    
    #[EnumCase('subscription')]
    case SUB = 'subscription';
    
    #[EnumCase('heren')]
    case HRN = 'heren';
    
    public static function toString(?string $value)
    {
      return match($value) {
          self::SUB => 'subscription',
          self::HRN => 'heren'
      };
    }
    // public function getId()
    // {
    //     return $this->name;
    // }
    
    public function getValue()
    {
        return $this->value;
    }

}