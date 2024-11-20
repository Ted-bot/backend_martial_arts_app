<?php

namespace App\Dto\Event;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CalendarItemDto
{
    public function __construct()
    {}
        #[Assert\Type('int')]
        public int $id = 0;
        
        #[Assert\Type('string')]
        public string $title = '';
        
        #[Assert\Type('string')]
        public string $description = '';
        
        // /** @var DateTimeInterface $startDate*/
        #[Assert\Type('string')]
        public DateTimeInterface $startDate;
        
        // /** @var DateTimeInterface $endDate*/
        #[Assert\Type('string')]
        public DateTimeInterface $endDate;
        
        #[Assert\Type('string')]
        public string $resource = '';
        
        #[Assert\Type('int')]
        public int $selectedEvent = 0;
    
}