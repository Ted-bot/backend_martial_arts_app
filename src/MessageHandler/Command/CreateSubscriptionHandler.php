<?php


namespace App\MessageHandler\Command;

use App\Service\SubscriptionUUID;
use App\Repository\SubscriptionRepository;
use App\Message\Command\CreateSubscriptionMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
final class CreateSubscriptionHandler
{
    private SubscriptionUUID $subscriptionUUID;
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionUUID $uuid,SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionUUID = $uuid;
        $this->subscriptionRepository = $subscriptionRepository;

    }


    public function __invoke(CreateSubscriptionMessage $createSubscriptionMessage)
    {
        dd($createSubscriptionMessage);
    }
}