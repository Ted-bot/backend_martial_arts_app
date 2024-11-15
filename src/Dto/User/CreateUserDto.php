<?php

declare(strict_types=1);

namespace App\Dto\User;

use App\Request\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class CreateUserDto extends AbstractJsonRequest
{
    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[Length(
        min: 2,
        max: 25,
        minMessage: 'Your first name must be at least {{ limit }} characters long!',
        maxMessage: 'first last name cannot be longer than {{ limit }} characters!',
    )]
    public readonly string $firstName;

    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[Length(
        min: 2,
        max: 25,
        minMessage: 'Your last name must be at least {{ limit }} characters long!',
        maxMessage: 'Your last name cannot be longer than {{ limit }} characters!',
    )]
    public readonly string $lastName;

    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[Email()]
    public readonly string $email;

    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[Length(
        min: 10,
        max: 20,
        minMessage: 'Your phone number must be at least {{ limit }} characters long!',
        maxMessage: 'Your phone number cannot be longer than {{ limit }} characters!',
    )]
    public readonly string $phoneNumber;
    
    #[Date()]
    #[NotBlank(message: 'This field cannot be empty')]
    public readonly string $dateOfBirth;

    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[Length(
        min: 4,
        max: 6,
        minMessage: 'Your gender is either male or female',
        maxMessage: 'Your gender identification cannot be longer than {{ limit }}, Your gender is either male or female!',
    )]
    public readonly string $gender;

    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[Length(
        min: 2,
        max: 50,
        minMessage: 'Your location must be at least {{ limit }} characters long',
        maxMessage: 'Your location cannot be longer than {{ limit }} characters',
    )]
    public readonly string $location;
    
    
    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_WEAK,
    ])]
    public readonly string $password;

    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[Length(
        min: 10,
        max: 250,
        minMessage: 'Your given reason for signing up up must be at least {{ limit }} characters long',
        maxMessage: 'Your given reason for signing up cannot be longer than {{ limit }} characters',
    )]
    public readonly string $conversion;
    
    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[Length(
        min: 1,
        max: 5,
        minMessage: 'CityId must have aleast {{ limit }} characters',
        maxMessage: 'CityId cannot be longer than {{ limit }} characters',
    )]
    public readonly string $cityId;


    #[NotBlank(message: 'This field cannot be empty')]
    #[Type('string')]
    #[Length(
        min: 1,
        max: 4,
        minMessage: 'StateId must have aleast {{ limit }} characters',
        maxMessage: 'StateId cannot be longer than {{ limit }} characters',
    )]
    public readonly string $stateId;

}