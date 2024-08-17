<?php

namespace App\RemoteEvent;

use App\Entity\StatusTransfer;
use App\Repository\ShopOrderRepository;
use App\Repository\StatusTransferRepository;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Psr\Log\LoggerInterface;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Exceptions\ApiException;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use App\ApiResource\MolliePaymentStatusEnum;

#[AsRemoteEventConsumer('MollieDirectPayment')]
final class MollieDirectPaymentWebhookConsumer implements ConsumerInterface
{
    private $logger;

    public function __construct(
        private LoggerInterface $transferEventLogger,
        // private StatusTransfer $statusTransfer,
        private EntityManager $entityManager,
        private ShopOrderRepository $soRepo,
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

        // get stored data from data base and update order
        // $this->shopOrderRepository
        
        // $this->logger->debug(
        //     'An event occurred in transfer remote event!',
        //     [
        //         'remote_event' => [
        //             'id' => $event->getId(),
        //             'name' => '???',
        //         ]
        //     ]
        // );

        // dd(['test' => 'Listener' ,'event' => $event]);        
        
        // Implement your own logic here
        try {
            /*
            * Initialize the Mollie API library with your API key or OAuth access token.
            */
            // require "../Class/initialize.php";
            $mollie = new MollieApiClient();
            $mollie->setApiKey('test_hqd6Sq8D72UUKebcT9RnVhkd6k96x2');
        
            /*
             * After your webhook has been called with the order ID in its body, you'd like
             * to handle the order's status change. This is how you can do that.
             *
             * See: https://docs.mollie.com/reference/v2/orders-api/get-order
             */
            $payment = $mollie->payments->get($event->getId());

            $existingOrder = $this->stRepo->findOneBy(['transferId' => $payment->id]);
            $shopOrderUpdate = $this->soRepo->findOneBy(['id' => $existingOrder]);            

            $statusTransfer = new StatusTransfer();
            
            $statusTransfer->setUserOrder($shopOrderUpdate);
            $statusTransfer->setStatus(MolliePaymentStatusEnum::PAID);
            $statusTransfer->setTransferId($event->getId());
            // $statusTransfer->setUserOrder($event->getId());

            // replace with class instead of table data
            $statusPayment = MolliePaymentStatusEnum::tryFrom($payment->status);

            
            if(MolliePaymentStatusEnum::PAID === $statusPayment){
                $shopOrderUpdate->setOrderStatus(MolliePaymentStatusEnum::PAID);
            }
            
            // dd([
            //     'order Status = paid? ' => MolliePaymentStatusEnum::PAID === $statusPayment,
            //     'Shop Order Status ' => $shopOrderUpdate->getOrderStatus()
            // ]);
            
            /*
            * Update the order in the database.
            */
            // database_write($orderId, $order->status);  
            foreach([$shopOrderUpdate, $statusTransfer] as $updateData){
                $this->entityManager->persist($updateData);
                $this->entityManager->flush();  
            }  
            
            // dd([
                //     'mollie_payment_id' => $payment->id,
                //     'isPaid' => $payment->isPaid()
                // ]);
 
            if ($payment->isPaid() || $payment->isAuthorized()) {
                /*
                * The order is paid or authorized
                * At this point you'd probably want to start the process of delivering the product to the customer.
                */
                // $consumerPaid = new MessageComponent(id: $payment->id, status: $payment->status);
                
                $this->logger->debug(
                    'An event occurred in transfer remote event!',
                    [
                        'remote_event' => [
                            'id' => $payment->id,
                            'name' => $payment->status,
                        ]
                    ]
                );

                // $this->bus->dispatch(new SendWebhookMessage($consumerPaid));

            } elseif ($payment->isCanceled() || $payment->isFailed()) {
                /*
                * The order is canceled.
                */
                // $consumerCancelled = new MessageComponent(id: $payment->id, status: $payment->status);
                
                $this->logger->debug(
                    'An event occurred in transfer remote event!',
                    [
                        'remote_event' => [
                            'id' => $payment->id,
                            'name' => $payment->status,
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
                'id_mollie_payment_error' => $event->getId()
            ]);
        }
    }
}
