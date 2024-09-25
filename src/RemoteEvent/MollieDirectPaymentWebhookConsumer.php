<?php

namespace App\RemoteEvent;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\OrderLine;
use App\Entity\ShopOrder;
use App\Entity\Subscription;
use App\Repository\OrderLineRepository;
use Psr\Log\LoggerInterface;
use App\Entity\StatusTransfer;
use App\Enum\CategoryTypeEnum;
use Mollie\Api\MollieApiClient;
use App\Repository\UserRepository;
use App\Enum\MolliePaymentStatusEnum;
use App\Repository\ProductRepository;
use App\Repository\ShopOrderRepository;
use Mollie\Api\Exceptions\ApiException;
use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\Collection;
use App\Repository\StatusTransferRepository;
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
        private OrderLineRepository $orderLinesRepo,
        private UserRepository $userRepo,
        private ProductRepository $prRepo,
        private StatusTransferRepository $stRepo,
        private SubscriptionRepository $subRepo,
    )
    {
        $this->logger = $transferEventLogger;
    }

    public function consume(RemoteEvent $event): void
    {         
        $statusTransfer = new StatusTransfer();

        try {
            $mollie = new MollieApiClient();
            $mollie->setApiKey('test_hqd6Sq8D72UUKebcT9RnVhkd6k96x2');
            $payment = $mollie->payments->get($event->getId());

            /** @var StatusTransfer $previousTransfer Object */
            $previousTransfer = $this->entityManager->getRepository(StatusTransfer::class)->findOneBy(['transferId' => $payment->id]);
            
            /** @var ShopOrder $shopOrderUpdate Object */
            $shopOrderUpdate = $previousTransfer->getUserOrder();

            if ($payment->isPaid() || $payment->isAuthorized()) {

                // $statusPayment = MolliePaymentStatusEnum::tryFrom($payment->status);
                $statusTransfer->setUserOrder($shopOrderUpdate);
                $statusTransfer->setStatus(MolliePaymentStatusEnum::PAID);
                $statusTransfer->setTransferId($event->getId());
                $statusTransfer->setCustomer($previousTransfer->getCustomer());

                // $shopOrderUpdate->setStatus($statusPayment);
                $this->logger->debug(
                    'An event occurred in transfer remote event!',
                    [
                        'remote_event' => [
                            'id' => $payment->id,
                            'statusMollie' => $payment->status,
                            'shopOrderId' =>  $shopOrderUpdate->getId(),
                            'shopOrder' =>  $shopOrderUpdate,
                            // 'getUpdatedStatus' =>  $statusPayment,
                            'time' => new \DateTime("now", new \DateTimeZone("Europe/Amsterdam")),
                        ]
                    ]
                );
                // if($statusPayment !== null){
                // dd('test');
                $shopOrderUpdate->setOrderStatus(MolliePaymentStatusEnum::PAID);
                // }                    
                
                $this->entityManager->persist($statusTransfer);
                $this->entityManager->flush();  
                
                // $this->entityManager->persist($shopOrderUpdate);
                $this->entityManager->flush();  
                
                $this->checkForSubscription($shopOrderUpdate->getId(), $event->getId());
                

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
                
                foreach([$shopOrderUpdate, $statusTransfer] as $key => $updateData){
                    $this->entityManager->persist($updateData);
                    $this->entityManager->flush();  
                }  
                
                $this->logger->debug(
                    'An event occurred in transfer remote event!',
                    [
                        'remote_event' => [
                            'id' => $payment->id,
                            'statusMollie' => $payment->status,
                            'statusConverted' =>  $statusPayment,
                            'time' => new \DateTime("now", new \DateTimeZone("Europe/Amsterdam")),
                        ]
                ]);
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

    public function checkForSubscription($id, $transferId): void
    {
        /** @var ShopOrder $shopOrder */
        $shopOrder = $this->soRepo->findOneBy(['id' => $id]); 

        /** @var Collection<int, OrderLine> */        
        $lines = $shopOrder->getOrderLines();

        foreach($lines as $line){

            /** @var Product $product */
            $product = $line->getProduct();

            if($product->getCategory() !== CategoryTypeEnum::SUB){
                return;
            }
            
            /** @var Subscription $subscription */
            $subscription = $this->entityManager->getRepository(Subscription::class)->findOneBy(['subOwnedBy' => $shopOrder->getOrderOwnedBy()], ['id' => 'DESC']);
            // $subscription = $this->subRepo->findOneBy(['subOwnedBy' => $shopOrder->getOrderOwnedBy()]);
            $subscription->setUpdatedAt();
            $subscription->setStatus(MolliePaymentStatusEnum::PAID);
            $subscription->setTransferId($transferId);
            $this->entityManager->persist($subscription);
            $this->entityManager->flush();
        }
    }
}
