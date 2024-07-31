<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class CreateUserRequest extends AbstractJsonRequest
{
    #[NotBlank(message: 'I dont like this field empty')]
    #[Type('string')]
    #[Length(
        min: 2,
        max: 25,
        minMessage: 'Your first name must be at least {{ limit }} characters long!',
        maxMessage: 'first last name cannot be longer than {{ limit }} characters!',
    )]
    public readonly string $firstName;

    #[NotBlank(message: 'I dont like this field empty')]
    #[Type('string')]
    #[Length(
        min: 2,
        max: 25,
        minMessage: 'Your last name must be at least {{ limit }} characters long!',
        maxMessage: 'Your last name cannot be longer than {{ limit }} characters!',
    )]
    public readonly string $lastName;

    #[NotBlank()]
    #[Type('string')]
    #[Email()]
    public readonly string $email;

    #[NotBlank()]
    #[Type('string')]
    #[Length(
        min: 10,
        max: 13,
        minMessage: 'Your phone number must be at least {{ limit }} characters long!',
        maxMessage: 'Your phone number cannot be longer than {{ limit }} characters!',
    )]
    public readonly string $phoneNumber;
    
    #[NotBlank()]
    #[Date()]
    public readonly string $dateOfBirth;

    #[NotBlank()]
    #[Type('string')]
    #[Length(
        min: 4,
        max: 6,
        minMessage: 'Your gender is either male or female',
        maxMessage: 'Your gender identification cannot be longer than {{ limit }}, Your gender is either male or female!',
    )]
    public readonly string $gender;

    #[NotBlank()]
    #[Type('string')]
    #[Length(
        min: 2,
        max: 50,
        minMessage: 'Your location must be at least {{ limit }} characters long',
        maxMessage: 'Your location cannot be longer than {{ limit }} characters',
    )]
    public readonly string $location;
    
    
    #[NotBlank()]
    #[Type('string')]
    #[PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_WEAK,
    ])]
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
    
    #[NotBlank()]
    #[Type('string')]
    #[Length(
        min: 1,
        max: 5,
        minMessage: 'CityId must have aleast {{ limit }} characters',
        maxMessage: 'CityId cannot be longer than {{ limit }} characters',
    )]
    public readonly string $cityId;


    #[NotBlank()]
    #[Type('string')]
    #[Length(
        min: 1,
        max: 4,
        minMessage: 'StateId must have aleast {{ limit }} characters',
        maxMessage: 'StateId cannot be longer than {{ limit }} characters',
    )]
    public readonly string $stateId;

}