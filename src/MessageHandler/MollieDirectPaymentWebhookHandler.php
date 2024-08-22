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
        // $client = $this->forward("App\Controller\YourController::ControllerFunction");
        // $this->client->request('POST', 'http://localhost/webhook/mollie_direct_payment', ['verify_peer' => false, 'body' => ['order_id' => $mollieDirectPayment->getOrderId()]]);
        // $this->client->request('POST', 'http://localhost/webhook/mollie_direct_payment', ['verify_peer' => false, 'body' => ['order_id' => $mollieDirectPayment->getOrderId()]]);
        // $this->client->request('POST', 'http://localhost/webhook/mollie_direct_payment', ['body' => ['order_id' => $mollieDirectPayment->getOrderId()]]);
        // dd(['handler/MollieDirectPaymentWebhookHandler' => '$mollieDirectPayment->order_id']);
        // send message order id is updated
        // $mollieDirectPayment->order_id;
    }
}