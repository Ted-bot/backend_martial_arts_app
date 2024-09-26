<?php


namespace App\Service;

use Symfony\Component\Uid\UuidV4;

class SubscriptionUUID
{    
    private UuidV4 $uuidFactory;

    public function __construct(){
        $this->uuidFactory = new UuidV4();
    }
    public function create(): uuidV4
    {
        return $this->uuidFactory;
    }
    
    public function toString(): string{
        /** @var UuidV4 $uuid */
        $uuid = $this->uuidFactory;
        
        return $uuid->__toString();
    }

    public static function fromString(string $uuidV4 = '7038677a-dbf9-4ad2-8b97-841787a6c33d')
    {
        return UuidV4::fromString($uuidV4);
    }
}