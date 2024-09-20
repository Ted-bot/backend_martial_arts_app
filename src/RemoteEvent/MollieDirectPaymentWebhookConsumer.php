<?php

namespace App\RemoteEvent;

use App\Entity\User;
use App\Entity\ShopOrder;
use App\Entity\StatusTransfer;
use Psr\Log\LoggerInterface;
use Mollie\Api\MollieApiClient;
use App\Repository\UserRepository;
use App\Repository\ShopOrderRepository;
use App\Repository\StatusTransferRepository;
use App\Enum\MolliePaymentStatusEnum;
use Mollie\Api\Exceptions\ApiException;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;

#[AsRemoteEventConsumer('MollieDirectPayment')]
final class MollieDirectPaymentWebhookConsumer implements ConsumerInterface
{
    private $logger;

    public function __construct(
        private LoggerInterface $transferEventLogger,
        // private StatusTransfer $statusTransfer,
        private EntityManager $entityManager,
        private ShopOrderRepository $soRepo,
        private UserRepository $userRepo,
        private StatusTransferRepository $stRepo
    )
    {
        $this->logger = $transferEventLogger;
    }

    public function consume(RemoteEvent $event): void
    {
        // Implement your own logic here
        if(!$event instanceof RemoteEvent)
        {
            dd(['failed' => 'Webhook not Accepted']);     
            // return;
        }
        // dd(['failed' => 'Webhook not Accepted']);    
        $statusTransfer = new StatusTransfer();            
        $mollieCustomerId = ''; 

        try {
            $mollie = new MollieApiClient();
            $mollie->setApiKey('test_hqd6Sq8D72UUKebcT9RnVhkd6k96x2');
            $payment = $mollie->payments->get($event->getId());

            /** @var StatusTransfer $previousTransfer Object */
            $previousTransfer = $this->stRepo->findOneBy(['transferId' => $payment->id]);

            /** @var ShopOrder $shopOrderUpdate Object */
            $shopOrderUpdate = $this->soRepo->findOneBy(['id' => $previousTransfer->getUserOrder(), 'orderStatus' => MolliePaymentStatusEnum::OPEN]);            

            if ($payment->isPaid() || $payment->isAuthorized()) {
                /*
                * The order is paid or authorized
                * At this point you'd probably want to start the process of delivering the product to the customer.
                */
                // $consumerPaid = new MessageComponent(id: $payment->id, status: $payment->status);

                $statusPayment = MolliePaymentStatusEnum::tryFrom($payment->status);
                // $statusPayment = MolliePaymentStatusEnum::tryFrom($payment->status);
                
                $statusTransfer->setUserOrder($shopOrderUpdate);
                $statusTransfer->setStatus($statusPayment);
                $statusTransfer->setTransferId($event->getId());
                $statusTransfer->setCustomer($previousTransfer->getCustomer());

                $shopOrderUpdate->setOrderStatus($statusPayment);
                
                foreach([$shopOrderUpdate, $statusTransfer] as $updateData){
                    $this->entityManager->persist($updateData);
                    $this->entityManager->flush();  
                }  

                $this->logger->debug(
                    'An event occurred in transfer remote event!',
                    [
                        'remote_event' => [
                            'id' => $payment->id,
                            'status' => $payment->status,
                            'time' => new \DateTime("now", new \DateTimeZone("Europe/Amsterdam")),
                        ]
                    ]
                );


                // $this->bus->dispatch(new SendWebhookMessage($consumerPaid));

            } elseif ($payment->isCanceled() || $payment->isFailed()) {
                /*
                * The order is canceled.
                */
                // $consumerCancelled = new MessageComponent(id: $payment->id, status: $payment->status);
              
                $statusPayment = MolliePaymentStatusEnum::tryFrom($payment->status);
                // $statusPayment = MolliePaymentStatusEnum::tryFrom($payment->status);
                
                $statusTransfer->setUserOrder($shopOrderUpdate);
                $statusTransfer->setStatus($statusPayment);
                $statusTransfer->setTransferId($event->getId());
                $statusTransfer->setCustomer($previousTransfer->getCustomer());

                $shopOrderUpdate->setOrderStatus($statusPayment);
                
                foreach([$shopOrderUpdate, $statusTransfer] as $updateData){
                    $this->entityManager->persist($updateData);
                    $this->entityManager->flush();  
                }  

                // dd(['succes']);

                $this->logger->debug(
                    'An event occurred in transfer remote event!',
                    [
                        'remote_event' => [
                            'id' => $payment->id,
                            'status' => $payment->status,
                            'time' => new \DateTime("now", new \DateTimeZone("Europe/Amsterdam")),
                        ]
                ]);

                $this->logger->debug(
                    'An event occurred in transfer remote event!',
                    [
                        'remote_event' => [
                            'id' => $payment->id,
                            'status' => $payment->status,
                        ]
                    ]
                );

                // $this->bus->dispatch(new SendWebhookMessage($consumerCancelled));

            } elseif ($payment->isExpired()) {
                /*
                * The order is expired.
                */
                // $consumerPaymentExpired = new MessageComponent(id: $payment->id, status: $payment->status);
                

                // $this->bus->dispatch(new SendWebhookMessage($consumerPaymentExpired));
            
            } elseif ($payment->isOpen()) {
                /*
                * The order is pending.
                */
                // $consumerPaymentPending = new MessageComponent(id: $payment->id, status: $payment->status);

                // $this->bus->dispatch(new SendWebhookMessage($consumerPaymentPending));

            } elseif ($payment->isPending()) {
                /*
                * The order is pending.
                */
                // $consumerPaymentPending = new MessageComponent(id: $payment->id, status: $payment->status);

                // $this->bus->dispatch(new SendWebhookMessage($consumerPaymentPending));

            }



        } catch (ApiException $e) {
            echo "API call failed: " . htmlspecialchars($e->getMessage());
            $this->logger->debug('Webhook Consumer Error: while trying to handle transfer request!', [
                'payment_id_error' => $event->getId()
            ]);
        }
    }
}
