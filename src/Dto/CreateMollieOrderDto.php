<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use App\Dto\OrderAmountDto;
class CreateMollieOrderDto
{
    public function __construct(
        // #[Assert\Collection([
        //         'value' => [
        //             new Assert\NotBlank,
        //             new Assert\Type('numeric'),
        //         ],
        //         'currency' => new Assert\Length(
        //                 max: 3,
        //                 maxMessage: 'Your currency should contain {{ limit }} characters'
        //         ),
        //     ]
        // )]
        // #[JMS\Type(OrderAmountDto::class)]
        // #[JMS\Type("App\Dto\OrderAmountDto")]
        // #[JMS\SerializedName('amount')]
        #[Assert\Valid]
        public readonly ?OrderAmountDto $amount,
        // protected readonly array $amount = ['value' => '...', 'currency' => '...'],

        // // #[Assert\NotBlank]
        // #[Assert\Collection(
        //     fields: [
        //         'streetAndNumber' => new Assert\Required([
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 50,
        //                 maxMessage: 'Your street and number should contain max {{ limit }} characters'
        //             ),
        //         ]),
        //         'postalCode' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 7,
        //                 maxMessage: 'Your postal code should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'city' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 50,
        //                 maxMessage: 'Your city should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'country' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 2,
        //                 maxMessage: 'Your city should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'givenName' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 25,
        //                 maxMessage: 'Your given name should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'familyName' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 25,
        //                 maxMessage: 'Your family name should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'email' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Email,
        //         ),
        //     ],
        // )]
        // protected readonly array $billingAddress,

        // // #[Assert\NotBlank]
        // #[Assert\Collection(
        //     fields: [
        //         'streetAndNumber' => new Assert\Required([
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 50,
        //                 maxMessage: 'Your street and number should contain max {{ limit }} characters'
        //             ),
        //         ]),
        //         'postalCode' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 7,
        //                 maxMessage: 'Your postal code should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'city' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 50,
        //                 maxMessage: 'Your city should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'country' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 2,
        //                 maxMessage: 'Your city should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'givenName' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 25,
        //                 maxMessage: 'Your given name should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'familyName' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Length(
        //                 max: 25,
        //                 maxMessage: 'Your family name should contain max {{ limit }} characters'
        //             ),
        //         ),
        //         'email' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Email,
        //         ),
        //     ],
        // )]
        // protected readonly array $shippingAddress,

        // #[Assert\NotBlank]
        // #[Assert\Collection(
        //     fields: [
        //         'some' => new Assert\Optional([
        //             new Assert\type('string'),
        //         ]),
        //     ],
        // )]
        // protected readonly array $metadata,

        #[Assert\NotBlank]
        #[Assert\Date()]
        protected readonly string $consumerDateOfBirth,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        protected readonly string $locale,

        #[Assert\NotBlank]
        #[Assert\Url]
        protected readonly string $redirectUrl,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        protected readonly string $webhookUrl,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        protected readonly string $method,

        // #[Assert\Collection(
        //     fields: [
        //         'sku' => new Assert\Required([
        //             new Assert\NotBlank,
        //             new Assert\Type('string'),
        //         ]),
        //         'name' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Type('string'),
        //         ),
        //         'productUrl' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Url,
        //         ),
        //         'imageUrl' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Url,
        //         ),
        //         'quantity' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Type('numeric'),
        //         ),
        //         'vatRate' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Type('numeric'),
        //         ),
        //         'unitPrice' => new Assert\Required(
        //             new Assert\NotBlank,
        //             new Assert\Type('numeric'),
        //         ),
        //         'totalAmount' => new Assert\Required([
        //             new Assert\NotBlank,
        //             new Assert\Type('numeric'),
        //         ]),
        //         'discountAmount' => new Assert\Required([
        //             new Assert\NotBlank,
        //             new Assert\Type('numeric'),
        //         ]),
        //         'vatRateAmount' => new Assert\Required([
        //             new Assert\NotBlank,
        //             new Assert\Type('numeric'),
        //         ]),
        //     ],
        // )]
        // protected readonly array $lines,
    )
    {}
}