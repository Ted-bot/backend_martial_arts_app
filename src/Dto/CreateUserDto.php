<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class CreateUserDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 2,
            max: 25,
            minMessage: 'Your phone number must be at least {{ limit }} characters long',
            maxMessage: 'Your phone number cannot be longer than {{ limit }} characters',
        )]
        public readonly string $first_name,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 2,
            max: 25,
            minMessage: 'Your last name must be at least {{ limit }} characters long',
            maxMessage: 'Your last name cannot be longer than {{ limit }} characters',
        )]
        public readonly string $last_name,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Email(
            message: 'The email {{ value }} is not a valid email.',
        )]
        public readonly string $email,
        
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 2,
            max: 50,
            minMessage: 'Your location must be at least {{ limit }} characters long',
            maxMessage: 'Your location cannot be longer than {{ limit }} characters',
        )]
        public readonly string $location,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 4,
            max: 6,
            minMessage: 'Your gender is either male or female',
            maxMessage: 'Your gender identification cannot be longer than {{ limit }}, Your gender is either male or female!',
        )]
        public readonly string $gender,

        #[Assert\NotBlank]
        #[Assert\Date]
        public readonly string $date_of_birth,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 10,
            max: 13,
            minMessage: 'Your phone number must be at least {{ limit }} characters long',
            maxMessage: 'Your phone number cannot be longer than {{ limit }} characters',
        )]
        public readonly string $phone_number,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\NoSuspiciousCharacters]
        #[Assert\Length(
            min: 10,
            max: 250,
            minMessage: 'Your given reason for signing up up must be at least {{ limit }} characters long',
            maxMessage: 'Your given reason for signing up cannot be longer than {{ limit }} characters',
        )]
        public readonly string $conversion,

        #[Assert\NotBlank]
        #[Assert\NotCompromisedPassword]
        #[Assert\Type('string')]
        #[Assert\PasswordStrength([
            'minScore' => PasswordStrength::STRENGTH_WEAK,
        ])]
        public readonly string $password,

        // #[Assert\NotBlank]
        // #[Assert\Type('string')]
        // public readonly string $category,

        // #[Assert\Type('array')]
        // public ?array $tags = null,
    ) {
    }
}