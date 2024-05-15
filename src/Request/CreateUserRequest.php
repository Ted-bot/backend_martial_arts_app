<?php

declare(strict_types=1);

namespace App\Request;

// use App\Validator\CreditCard;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
// use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Date;

class CreateUserRequest extends AbstractJsonRequest
{
    #[NotBlank(message: 'I dont like this field empty')]
    #[Type('string')]
    public readonly string $firstName;

    #[NotBlank(message: 'I dont like this field empty')]
    #[Type('string')]
    public readonly string $lastName;

    #[NotBlank()]
    #[Type('string')]
    #[Email()]
    public readonly string $email;

    #[NotBlank()]
    #[Type('string')]
    public readonly string $phoneNumber;
    
    #[NotBlank()]
    #[Date()]
    public readonly string $dateOfBirth;

    #[NotBlank()]
    #[Type('string')]
    public readonly string $gender;

    #[NotBlank()]
    #[Type('string')]
    public readonly string $location;
    
    
    #[NotBlank()]
    #[Type('string')]
    public readonly string $password;

    #[NotBlank()]
    #[Type('string')]
    #[Length(
        min: 10,
        max: 250,
        minMessage: 'Your given reason for signing up up must be at least {{ limit }} characters long',
        maxMessage: 'Your given reason for signing up cannot be longer than {{ limit }} characters',
    )]
    public readonly string $conversion;

}