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

    public function cancelUserSubscription(string $subscriptionId)
    {
        $customer = $this->mollieApi->customers->get($subscriptionId);

        // $canceledSubscription = 
        $customer->cancelSubscription($subscriptionId);

        // dd('customer', $canceledSubscription);
    }
}