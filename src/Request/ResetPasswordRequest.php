<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class ResetPasswordRequest extends AbstractJsonRequest
{  
    #[NotBlank()]
    #[Type('string')]
    #[PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_WEAK,
    ])]
    public readonly string $password;

}