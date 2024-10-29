<?php
declare(strict_types=1);

namespace App\Dto\UserSubscription;

use App\Dto\MollieClient\OrderAmountDto;
use App\Dto\MollieClient\OrderAddressDto;
use App\Dto\MollieClient\OrderMetaDataDto;
use App\Dto\MollieClient\OrderSubscriptionDto;
use Mollie\Api\Types\SequenceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;

class CancellSubscription
{     
    public function __construct(
        // #[Assert\Type('uuid')]
        #[Assert\NotBlank]
        public $uuid,

    )
    {}
}