<?php

namespace App\Dto\UserDashboard;

use Symfony\Component\Validator\Constraints as Assert;

class DashBoardDto
{
    public function __construct(
        #[Assert\Type('int')]
        public int $tokens_owned = 0,

        #[Assert\Type('string')]
        public $name_subscription = '',

        #[Assert\Type('string')]
        public $start = '',

        #[Assert\Type('string')]
        public $end = '',

        #[Assert\Type('string')]
        public $userFullName = '',

        #[Assert\Type('int')]
        public $sessions_followed = 0,

        #[Assert\Type('string')]
        public $next_session = 'no training day selected!',
    )
    {}
}