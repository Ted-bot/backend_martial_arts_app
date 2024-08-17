<?php

declare(strict_types=1);

namespace App\ApiResource;

// use App\Class\Trait\EnumToArray;
use ApiPlatform\Metadata\Get;
use Elao\Enum\Attribute\EnumCase;
use ApiPlatform\Metadata\Operation;
// use App\DBAL\Types\SQLEnumTypeTrait;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\EnumApiResourceTrait;
use Symfony\Component\Serializer\Annotation\Groups;


#[
  ApiResource(normalizationContext: ['groups' => ['read']]),
  GetCollection(provider: Status::class.'::getCases'), 
  Get(provider: Status::class.'::getCase'),
]
enum MolliePaymentStatusEnum: string
{
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
    
    #[EnumCase('Expired')]
    case EXPIRED = 'expired';
    
    #[EnumCase('Failed')]
    case FAILED = 'failed';

    use EnumApiResourceTrait;

    public function getId()
    {
        return $this->name;
    }
    
    public function getValue()
    {
        return $this->value;
    }

  //     public function toString()
  // {
  //   return match($this) {
  //       self::PAID => 'paid',
  //       self::REFUNDING => 'refunding',
  //       self::PROCESSING => 'processing',
  //       self::CANCELLED => 'cancelled',
  //       self::OPEN => 'open',
  //       self::PENDING => 'pending',
  //       self::AUTHORIZED => 'authorized',
  //       self::EXPIRED => 'expired',
  //       self::FAILED => 'failed',
  //   };
  // }

  //   public function toString()
  // {
  //   return match($this) {
  //       self::PAID => 'paid',
  //       self::REFUNDING => 'refunding',
  //       self::PROCESSING => 'processing',
  //       self::CANCELLED => 'cancelled',
  //       self::OPEN => 'open',
  //       self::PENDING => 'pending',
  //       self::AUTHORIZED => 'authorized',
  //       self::EXPIRED => 'expired',
  //       self::FAILED => 'failed',
  //   };
  // }

    // public const TYPES_STATUS = [
    //     self::PAID => 'paid',
    //     self::REFUNDING => 'refunding',
    //     self::PROCESSING => 'cancelled',
    //     self::CANCELLED => 'cancelled',
    //     self::OPEN => 'open',
    //     self::PENDING => 'pending',
    //     self::AUTHORIZED => 'authorized',
    //     self::EXPIRED => 'expired',
    //     self::FAILED => 'failed',        
    // ];    
}