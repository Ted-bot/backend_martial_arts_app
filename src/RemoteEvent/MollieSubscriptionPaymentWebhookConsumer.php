<?php

namespace App\RemoteEvent;

use App\Entity\Subscription;
use App\Enum\MolliePaymentStatusEnum;
use Psr\Log\LoggerInterface;
use Mollie\Api\MollieApiClient;
use App\Repository\SubscriptionRepository;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;

#[AsRemoteEventConsumer('MollieSubscriptionPayment')]
final class MollieSubscriptionPaymentWebhookConsumer implements ConsumerInterface
{
    private $logger;
    public function __construct(
        private SubscriptionRepository $subscriptionRepo,
        private EntityManager $entityManager,
        private LoggerInterface $subscriptionEventLogger
    )
    {
        $this->logger = $subscriptionEventLogger;
    }

    public function consume(RemoteEvent $event): void
    {
        // Implement your own logic here

        // dd(['event' => $event]);

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
            // $orderId = $subscription->metadata->order_id;

            /*
            * Update the order in the database.
            */
            // database_write($orderId, $subscription->status);

            if ($subscription->isPaid() && ! $subscription->hasRefunds() && ! $subscription->hasChargebacks()) {
                /*
                * The subscription is paid and isn't refunded or charged back.
                * At this point you'd probably want to start the process of delivering the product to the customer.
                */

                /** @var Subscription $subscriptionUser */
                $subscriptionUser = $this->subscriptionRepo->findOneBy(['uuid' => $subscriptionId]);
                $subscriptionUser->setUpdatedAt();
                $subscriptionUser->setStatus(MolliePaymentStatusEnum::PAID);
                $subscriptionUser->setTransferId($paymentId);

                $this->entityManager->persist($subscriptionUser);
                $this->entityManager->flush();  

                $this->logger->debug(
                    'An paid: event occurred in Subscription remote event!',
                    [
                        'remote_event' => [
                            'TransferId' => $paymentId,
                            'subscriptionId' => $subscriptionId,
                            'time' => new \DateTime("now", new \DateTimeZone("Europe/Amsterdam")),
                        ]
                    ]
                );
                                
            } elseif ($subscription->isOpen()) {
                /*
                * The subscription is open.
                */
            } elseif ($subscription->isPending()) {
                /*
                * The subscription is pending.
                */
            } elseif ($subscription->isFailed()) {
                $this->logger->debug(
                    'An failed: event occurred in Subscription remote event!',
                    [
                        'remote_event' => [
                            'TransferId' => $paymentId,
                            'subscriptionId' => $subscriptionId,
                            'time' => new \DateTime("now", new \DateTimeZone("Europe/Amsterdam")),
                        ]
                    ]
                );
                /*
                * The subscription has failed.
                */
            } elseif ($subscription->isExpired()) {
                
                /*
                * The subscription is expired.
                */
            } elseif ($subscription->isCanceled()) {
                $this->logger->debug(
                    'An cancelled: event occurred in Subscription remote event!',
                    [
                        'remote_event' => [
                            'TransferId' => $paymentId,
                            'subscriptionId' => $subscriptionId,
                            'time' => new \DateTime("now", new \DateTimeZone("Europe/Amsterdam")),
                        ]
                    ]
                );
                /*
                * The subscription has been canceled.
                */
            } elseif ($subscription->hasRefunds()) {
                /*
                * The subscription has been (partially) refunded.
                * The status of the subscription is still "paid"
                */
            } elseif ($subscription->hasChargebacks()) {
                $this->logger->debug(
                    'An chargeBack: event occurred in Subscription remote event!',
                    [
                        'remote_event' => [
                            'TransferId' => $paymentId,
                            'subscriptionId' => $subscriptionId,
                            'time' => new \DateTime("now", new \DateTimeZone("Europe/Amsterdam")),
                        ]
                    ]
                );
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
