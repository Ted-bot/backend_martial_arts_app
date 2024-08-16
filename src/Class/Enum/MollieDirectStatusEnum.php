<?php

namespace App\Class\Enum;

use App\Class\trait\EnumToArray;

enum MollieDirectStatusEnum: string
{
    use EnumToArray;
    case PAID = 'paid';
    case REFUNDING = 'refunding';
    case PROCESSING = 'processing';
    case CANCELLED = 'cancelled';
    case OPEN = 'open';
    case PENDING = 'pending';
    case AUTHORIZED = 'authorized';
    case EXPIRED = 'expired';
    case FAILED = 'failed';

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