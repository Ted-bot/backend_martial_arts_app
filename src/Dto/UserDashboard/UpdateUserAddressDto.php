<?php
declare(strict_types=1);

namespace App\Dto\UserDashboard;

use App\Dto\MollieClient\OrderAmountDto;
use App\Dto\MollieClient\OrderAddressDto;
use App\Dto\MollieClient\OrderMetaDataDto;
use App\Dto\MollieClient\OrderSubscriptionDto;
use Mollie\Api\Types\SequenceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserAddressDto
{
    // unit_number
            // street_number
            // address_line
            // postal_code        
    public function __construct(
        #[Assert\NotBlank(allowNull:true)]
        #[Assert\Type('int')]
        public $address_id,

        #[Assert\Type('string')]
        public $unit_number,

        #[Assert\NotBlank]
        #[Assert\Type('numeric')]
        public $street_number,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 9,
            max: 50,
            minMessage: 'Your order number cannot be longer than {{ limit }} characters',
            maxMessage: 'Your order number cannot be longer than {{ limit }} characters',
        )]
        public $address_line,
        
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 6,
            max: 6,
            minMessage: 'Your postal code cannot be shorter than {{ limit }} characters',
            maxMessage: 'Your postal code cannot be longer than {{ limit }} characters',
        )]
        public $postal_code,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public $city,

        #[Assert\NotBlank]
        #[Assert\Type('numeric')]
        public $city_id,

        #[Assert\NotBlank]
        #[Assert\Type('numeric')]
        public $state_id,
    )
    {}
}