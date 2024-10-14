<?php

namespace App\Dto\Main;

use App\Entity\PostEvent;
use Symfony\Component\Validator\Constraints as Assert;

class EventResponseDto
{
    public function __construct(
        #[Assert\Type('string')]
        public string $message = '',
        
        #[Assert\Type('int')]
        public int $status = 200,
        
        /** @var PostEvent $showUserSelectedEvent */
        #[Assert\Type('valid')]
        public $showUserSelectedEvent = null,
    )
    {}
}