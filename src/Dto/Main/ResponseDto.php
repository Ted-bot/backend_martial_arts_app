<?php

namespace App\Dto\Main;

use Symfony\Component\Validator\Constraints as Assert;

class ResponseDto
{
    public function __construct(
        #[Assert\Type('string')]
        public string $message = '',
    )
    {}
}