<?php


namespace App\Service;

use Mollie\Api\MollieApiClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MollieApiService
{
    private $mollieApi;

    public function __construct(
        // #[Autowire(service: )]
        private MollieApiClient $mollieApiClient,
        #[Autowire('%mollie.test%')]
        private string $apiTestKey,
        #[Autowire('%mollie.live%')]
        private string $apiLiveKey
    ){
        $this->mollieApi = $this->mollieApiClient->setApiKey($apiTestKey);
    }

    public function cancelUserSubscription(string $customerId, string $transferId): null
    {
        $customer = $this->mollieApi->customers->get($customerId);

        // $canceledSubscription = 
        return $customer->cancelSubscription($transferId);

        // dd('customer', $canceledSubscription);
    }
}