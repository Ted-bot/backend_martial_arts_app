<?php


namespace App\Service;

use DateTimeZone;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Uid\Factory\UuidFactory;

class SubscriptionUUID
{    
    public function create(): Uuid
    {
        return (new UuidFactory())->create();
    }

    public function fromString(string $value): Uuid
    {
        return Uuid::fromString($value);
    }
}