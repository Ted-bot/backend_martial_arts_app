<?php

declare(strict_types=1);

namespace App\Dto\MollieClient;

use Symfony\Component\Validator\Constraints as Assert;

class OrderAddressDto {

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(
            max: 5,
            maxMessage: 'Your title should contain max {{ limit }} characters'
        )]
        public $title,
        
        #[Assert\Length(
            max: 25,
            maxMessage: 'Your organization should contain max {{ limit }} characters'
        )]
        public $organizationName,
        
        #[Assert\NotBlank]
        #[Assert\Length(
            max: 75,
            maxMessage: 'Your street and number should contain max {{ limit }} characters'
        )]
        public $streetAndNumber,
        
        #[Assert\Length(
            max: 75,
            maxMessage: 'Your street and number should contain max {{ limit }} characters'
        )]
        public $streetAdditional,

        #[Assert\NotBlank]
        #[Assert\Length(
            max: 7,
            maxMessage: 'Your postal code should contain max {{ limit }} characters'
        )]
        public $postalCode,

        #[Assert\NotBlank]
        #[Assert\Length(
            max: 50,
            maxMessage: 'Your city should contain max {{ limit }} characters'
        )]
        public $city,

        #[Assert\NotBlank]
        #[Assert\Length(
            max: 2,
            maxMessage: 'Your country id should contain max {{ limit }} characters'
        )]
        public $country,

        #[Assert\NotBlank]
        #[Assert\Length(
            max: 25,
            maxMessage: 'Your given name should contain max {{ limit }} characters'
        )]
        public string $givenName,

        #[Assert\NotBlank]
        #[Assert\Length(
            max: 25,
            maxMessage: 'Your given name should contain max {{ limit }} characters'
        )]
        public string $familyName,
        
        #[Assert\NotBlank]
        #[Assert\Length(
            max: 15,
            maxMessage: 'Your phone number should contain max {{ limit }} characters'
        )]
        public string $phone,
        
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Length(
            max: 25,
            maxMessage: 'Your region should contain max {{ limit }} characters'
        )]
        public $region,

        #[Assert\NotBlank]
        #[Assert\Email(
            message: 'The email {{ value }} is not a valid email.',
        )]
        public string $email,
    ){}
}