<?php


namespace App\Service;

use DateTimeZone;
use DateTimeImmutable;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Factory\UlidFactory;

class ProductUlid
{

    public function __construct(
        private UlidFactory $ulidFactory
    ){        
    }

    public function create(): Ulid
    {
        $datetime = new DateTimeImmutable();
        $currentTimeEvent = $datetime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        return $this->ulidFactory->create($currentTimeEvent);
    }
}