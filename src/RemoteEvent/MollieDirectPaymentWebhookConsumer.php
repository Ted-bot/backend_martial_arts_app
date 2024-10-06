<?php

namespace App\RemoteEvent;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\OrderLine;
use App\Entity\ShopOrder;
use App\Entity\UserProfile;
use App\Entity\Subscription;
use App\Entity\TokenManager;
use Psr\Log\LoggerInterface;
use App\Entity\StatusTransfer;
use App\Enum\CategoryTypeEnum;
use Mollie\Api\MollieApiClient;
use App\Enum\MolliePaymentStatusEnum;
use Mollie\Api\Exceptions\ApiException;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\RemoteEvent\RemoteEvent;
use App\Service\SubscriptionUUID AS TokenManagerUUID;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use App\Repository\StatusTransferRepository;

#[AsRemoteEventConsumer('MollieDirectPayment')]
final class MollieDirectPaymentWebhookConsumer implements ConsumerInterface
{
    private $logger;

    public function __construct(
        private LoggerInterface $transferEventLogger,
        private EntityManager $entityManager,
        private StatusTransferRepository $stRepo,
    )
    {
        $this->logger = $transferEventLogger;
    }

    public function consume(RemoteEvent $event): void
    {         
        // dd('Paid');
        // $statusTransfer = new StatusTransfer();

        try {
            $mollie = new MollieApiClient();
            $mollie->setApiKey('test_hqd6Sq8D72UUKebcT9RnVhkd6k96x2');
            $payment = $mollie->payments->get($event->getId());

            /** @var StatusTransfer $currentTransfer Object */
            $currentTransfer = $this->stRepo->findOneBy([
                'transferId' => $payment->id,
                 'status' => MolliePaymentStatusEnum::OPEN
            ]);

            /** @var ShopOrder $shopOrderUpdate Object */
            $shopOrderUpdate = $currentTransfer?->getUserOrder();

            // dd(['paymentId' => $payment->id,'currentTransfer' => $currentTransfer]);

            $statusPayment = MolliePaymentStatusEnum::tryFrom($payment->status);

            if ($payment->isPaid() || $payment->isAuthorized()) {
                // $statusPayment = MolliePaymentStatusEnum::tryFrom($payment->status);
                // $statusTransfer->setUserOrder($shopOrderUpdate);
                // $statusTransfer->setStatus(MolliePaymentStatusEnum::PAID);
                // $statusTransfer->setTransferId($event->getId());
                // $statusTransfer->setCustomer($currentTransfer->getCustomer());                
                $currentTransfer->setUserOrder($shopOrderUpdate);
                
                $currentTransfer->setStatus(MolliePaymentStatusEnum::PAID);
                $shopOrderUpdate->setOrderStatus($statusPayment);
                $currentTransfer->setTransferId($event->getId());
                $currentTransfer->setCustomer($currentTransfer->getCustomer());

                $shopOrderUpdate->setOrderStatus($statusPayment);

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

                // $this->entityManager->persist($statusTransfer);
                $this->entityManager->flush();  
                                
                $this->checkForSubscription($shopOrderUpdate->getId(), $event->getId());
                
                // $this->bus->dispatch(new SendWebhookMessage($consumerPaid));

            } elseif ($payment->isCanceled() || $payment->isFailed()) {
                /*
                * The order is canceled.
                */
                // $consumerCancelled = new MessageComponent(id: $payment->id, status: $payment->status);
              
                
                // $statusPayment = MolliePaymentStatusEnum::tryFrom($payment->status);
                
                $currentTransfer->setUserOrder($shopOrderUpdate);
                $currentTransfer->setStatus($statusPayment);
                $currentTransfer->setTransferId($event->getId());
                $currentTransfer->setCustomer($currentTransfer->getCustomer());

                $shopOrderUpdate->setOrderStatus($statusPayment);
                
                foreach([$shopOrderUpdate, $currentTransfer] as $key => $updateData){
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

    // set function in orderpage when user purchases
    // public function setPreviousSubscriptionAsExprired(array $uuidArray)
    // {
    //     foreach($uuidArray as $uuid){
    //         /** @var Subscription $previousSubscription Object */
    //         $previousSubscription = $this->entityManager->getRepository(Subscription::class)
    //         ->findby(['uuid' => $uuid]); // status => paid setTo expired

    //         $previousSubscription->setUpdatedAt();
    //         $previousSubscription->setStatus(MolliePaymentStatusEnum::EXPIRED);
    //     }

    //     $this->entityManager->flush();
    // }

    public function checkForSubscription($id, $transferId)
    {
        $paidStatus = MolliePaymentStatusEnum::PAID;

        /** @var ShopOrder $shopOrder */
        $shopOrder = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(
            ['id' => $id, 'orderStatus' => $paidStatus],
             ['id' => 'DESC']
        ); 

        /** @var Collection<int, OrderLine> */        
        $lines = $shopOrder->getOrderLines();
        
        $subscriptionIdArray = [];

        foreach($lines as $line){
            /** @var Product $product */
            $product = $line->getProduct();
            if($product->getCategory() !== CategoryTypeEnum::SUB){
                return;
            }
            
            /** @var Subscription[] $subscriptions */
            $subscriptions = $this->entityManager->getRepository(Subscription::class)
            ->findBy([
                'subscriptionOwnedBy' => $shopOrder->getOrderOwnedBy()->getId(),
                'subscribedProduct' => $line->getProduct(),
                'status' => MolliePaymentStatusEnum::OPEN],
                 ['id' => 'DESC']
            );

            // dd(['Subscription UUID FOund' => $subscription, 'owner' => $shopOrder->getOrderOwnedBy()->getId()]);
            foreach($subscriptions as $subscription){
                // dd([';hallo' => $subscription]);
                $subscriptionIdArray[] = $subscription->getUuid(); // create short uniq subscription slug
                $subscription->setUpdatedAt();
                $subscription->setStatus(MolliePaymentStatusEnum::PAID);
                $subscription->setTransferId($transferId);
                // $this->entityManager->persist($subscription);
                $this->entityManager->flush();
            }
        }
        $userId = $shopOrder->getOrderOwnedBy();
        $this->callTokenManager($userId, $subscriptionIdArray); //
    }   

    public function callTokenManager($userId, array $subscriptionIdArray){

        /** @var UserProfile $profileUser */
        $profileUser = $this->entityManager->getRepository(UserProfile::class)
        ->findOneBy(['userUniq' => $userId]);

        foreach($subscriptionIdArray as $subscriptionUUID){
            $tokenManager = new TokenManager();
            
            /** @var Subscription $subscription */
            $subscription = $this->entityManager->getRepository(Subscription::class)
            ->findOneBy(['uuid' => $subscriptionUUID]);
            
            $tokenManager->setUserProfile($profileUser);
            $tokenManager->setUuid($subscriptionUUID);
            $tokenManager->setTokens(100); // note: create service to calculate
            $tokenManager->setRelatedSubscription($subscription);
            
            $profileUser->addTokenManager($tokenManager);

            $this->entityManager->persist($profileUser);
            $this->entityManager->persist($tokenManager);
        }     
        $this->entityManager->flush();
    }
}
