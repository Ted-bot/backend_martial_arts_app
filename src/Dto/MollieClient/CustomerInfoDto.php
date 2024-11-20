<?php

declare(strict_types=1);

namespace App\Dto\MollieClient;

use App\Request\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

class CustomerInfoDto // extends AbstractJsonRequest
{
    public function __construct(

        //Customer Info
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 2,
            max: 40,
            minMessage: 'Your first and last name must be at least {{ limit }} characters long',
            maxMessage: 'Your first and last name cannot be longer than {{ limit }} characters',
        )]
        public readonly string $firstAndLastName,

        #[Assert\Type('string')]
        #[Assert\Email(
            message: 'The email {{ value }} is not a valid email.',
        )]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 10,
            max: 20,
            minMessage: 'Your phone number must be at least {{ limit }} characters long',
            maxMessage: 'Your phone number cannot be longer than {{ limit }} characters',
        )]
        public readonly string $phoneNumber,

        // #[Assert\Type('string')]
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Length(
            min: 0,
            max: 5,
            maxMessage: 'Your location cannot be longer than {{ limit }} characters',
        )]
        public $unitNumber,

        #[Assert\NotBlank]
        #[Assert\Type('int')]
        #[Assert\Length(
            min: 1,
            max: 6, //change to 6
            minMessage: 'Your street number cannot be empty',
            maxMessage: 'Your street number cannot be longer than {{ limit }} characters!',
        )]
        public int $streetNumber,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 5,
            max: 60,
            maxMessage: 'Your location cannot be longer than {{ limit }} characters',
        )]
        public readonly string $addressLine,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 4,
            max: 6,
            minMessage: 'Your postal code must be at least {{ limit }} characters long',
            maxMessage: 'Your postal code cannot be longer than {{ limit }} characters',
        )]
        public readonly string $postalCode,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 3,
            max: 30,
            minMessage: 'Your given location {{ limit }} characters long',
            maxMessage: 'Your given location be longer than {{ limit }} characters',
        )]
        public readonly string $city,

        // #[Assert\Length(
        //     min: 3,
        //     max: 15,
        //     minMessage: 'Your given state must be at least {{ limit }} characters long',
        //     maxMessage: 'Your given state cannot be longer than {{ limit }} characters',
        // )]
        #[Assert\Blank]
        #[Assert\Type('string')]
        public readonly string $region,


        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 1,
            max: 4,
            minMessage: 'Your given react state identifier must be at least {{ limit }} characters long',
            maxMessage: 'Your given react state identifier cannot be longer than {{ limit }} characters',
        )]
        public readonly string $reactStateNr,
        
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 1,
            max: 6,
            minMessage: 'Your given react city iientifier must be at least {{ limit }} characters long',
            maxMessage: 'Your given react city identifiere cannot be longer than {{ limit }} characters',
        )]
        public readonly string $reactCityNr,

    ) {
    }

}