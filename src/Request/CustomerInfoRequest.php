<?php

declare(strict_types=1);

namespace App\Request;

// use Symfony\Component\Mime\Address;
use App\Request\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

class CustomerInfoRequest extends AbstractJsonRequest
{
        //Customer Info
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 2,
            max: 40,
            minMessage: 'Your first and last name must be at least {{ limit }} characters long',
            maxMessage: 'Your first and last name cannot be longer than {{ limit }} characters',
        )]
        public string $firstAndLastName;

        #[Assert\Type('string')]
        #[Assert\Email(
            message: 'The email {{ value }} is not a valid email.',
        )]
        public string $email;

        #[Assert\NotBlank(message: 'This field cannot be empty')]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 10,
            max: 13,
            minMessage: 'Your phone number must be at least {{ limit }} characters long',
            maxMessage: 'Your phone number cannot be longer than {{ limit }} characters',
        )]
        public string $phoneNumber;

        #[Assert\Length(
            min: 0,
            max: 5,
            maxMessage: 'Your location cannot be longer than {{ limit }} characters',
        )]
        public  $unitNumber;

        #[Assert\NotBlank(message: 'This field cannot be empty')]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 1,
            max: 6, //change to 6
            minMessage: 'Your street number cannot be empty',
            maxMessage: 'Your street number cannot be longer than {{ limit }} characters!',
        )]
        public string $streetNumber;

        #[Assert\NotBlank(message: 'This field cannot be empty')]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 5,
            max: 50,
            minMessage: 'Set a valid location',
            maxMessage: 'Your location should not be longer than {{ limit }} characters',
        )]
        public string $addressLine;

        #[Assert\NotBlank(message: 'This field cannot be empty')]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 4,
            max: 6,
            minMessage: 'Your postal code must be at least {{ limit }} characters long',
            maxMessage: 'Your postal code cannot be longer than {{ limit }} characters',
        )]
        public string $postalCode;

        #[Assert\NotBlank(message: 'This field cannot be empty')]
        #[Assert\Type('string')]
        // #[Assert\Length(
        //     min: 3,
        //     max: 30,
        //     minMessage: 'Your given location {{ limit }} characters long',
        //     maxMessage: 'Your given location be longer than {{ limit }} characters',
        // )]
        public string $location;

        #[Assert\NotBlank(message: 'This field cannot be empty')]
        #[Assert\Type('string')]
        public string $region;

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        // #[Assert\Length(
        //     min: 1,
        //     max: 4,
        //     minMessage: 'Your given react state identifier must be at least {{ limit }} characters long',
        //     maxMessage: 'Your given react state identifier cannot be longer than {{ limit }} characters',
        // )]
        public string $reactStateNr;
        
        #[Assert\NotBlank]
        #[Assert\Type(type:'string', message: 'only string allowed')]
        // #[Assert\Length(
        //     min: 1,
        //     max: 6,
        //     minMessage: 'Your given react city iientifier must be at least {{ limit }} characters long',
        //     maxMessage: 'Your given react city identifiere cannot be longer than {{ limit }} characters',
        // )]
        public string $reactCityNr;


}