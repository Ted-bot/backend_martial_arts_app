<?php

namespace App\Dto\Main;

use Symfony\Component\Validator\Constraints as Assert;

class ResponseDto
{
    public function __construct(
        #[Assert\Type('string')]
        public string $message = 'Security: InValid Request Made!',
        
        #[Assert\Valid]        
        public $body = [],

        #[Assert\Type('int')]
        public int $status = 400,
    )
    {}
}