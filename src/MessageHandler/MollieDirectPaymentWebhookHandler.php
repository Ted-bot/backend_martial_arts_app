<?php

namespace App\MessageHandler;

use App\Enum\MollieDirectPaymentWebhook;
use App\Message\SendWebhookMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface as HttpClient;
// use Symfony\Component\HttpClient\HttpClient;

#[AsMessageHandler]
final class MollieDirectPaymentWebhookHandler
{
    public function __construct(
        private readonly HttpClient $client
    )
    {

    }
    public function __invoke(SendWebhookMessage $mollieDirectPayment)
    {
        dd(['WebhookMessageHandler' => $mollieDirectPayment->getOrderId()]);
    }
}