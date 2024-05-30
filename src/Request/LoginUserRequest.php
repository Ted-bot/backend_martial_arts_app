<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class LoginUserRequest extends AbstractJsonRequest
{
    #[NotBlank()]
    #[Type('string')]
    #[Email()]
    public readonly string $email;
    
    #[NotBlank()]
    #[Type('string')]
    #[PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_WEAK,
    ])]
    public readonly string $password;

}