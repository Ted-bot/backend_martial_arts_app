<?php

namespace App\RemoteEvent;

use Mollie\Api\MollieApiClient;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;

#[AsRemoteEventConsumer('MollieSubscriptionPayment')]
final class MollieSubscriptionPaymentWebhookConsumer implements ConsumerInterface
{
    public function __construct()
    {
    }

    public function consume(RemoteEvent $event): void
    {
        // Implement your own logic here

    try {
        /*
        * Initialize the Mollie API library with your API key.
        *
        * See: https://www.mollie.com/dashboard/developers/api-keys
        */
        $mollie = new MollieApiClient();
        $mollie->setApiKey('test_hqd6Sq8D72UUKebcT9RnVhkd6k96x2');
        $subscription = $mollie->payments->get($event->getId());

        $paymentId = $event->getId();
        $subscriptionId = $event->getName();
        /*
        * Retrieve the subscription's current state.
        */
        $subscription = $mollie->payments->get($paymentId);
        $orderId = $subscription->metadata->order_id;

        /*
        * Update the order in the database.
        */
        // database_write($orderId, $subscription->status);

        if ($subscription->isPaid() && ! $subscription->hasRefunds() && ! $subscription->hasChargebacks()) {
            /*
            * The subscription is paid and isn't refunded or charged back.
            * At this point you'd probably want to start the process of delivering the product to the customer.
            */

            
        } elseif ($subscription->isOpen()) {
            /*
            * The subscription is open.
            */
        } elseif ($subscription->isPending()) {
            /*
            * The subscription is pending.
            */
        } elseif ($subscription->isFailed()) {
            /*
            * The subscription has failed.
            */
        } elseif ($subscription->isExpired()) {
            /*
            * The subscription is expired.
            */
        } elseif ($subscription->isCanceled()) {
            /*
            * The subscription has been canceled.
            */
        } elseif ($subscription->hasRefunds()) {
            /*
            * The subscription has been (partially) refunded.
            * The status of the subscription is still "paid"
            */
        } elseif ($subscription->hasChargebacks()) {
            /*
            * The subscription has been (partially) charged back.
            * The status of the subscription is still "paid"
            */
        }
    } catch (\Mollie\Api\Exceptions\ApiException $e) {
        echo "API call failed: " . htmlspecialchars($e->getMessage());
    }
    }
}
