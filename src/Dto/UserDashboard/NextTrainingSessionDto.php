<?php

namespace App\Dto\UserDashboard;

use Symfony\Component\Validator\Constraints as Assert;

class NextTrainingSessionDto
{
    public function __construct(

        #[Assert\Type('string')]
        public $next_training_day = '',

        #[Assert\Type('string')]
        public $start = '',

        #[Assert\Type('string')]
        public $end = '',
        
        #[Assert\Type('string')]
        public $trainerName = '',
    )
    {}
}