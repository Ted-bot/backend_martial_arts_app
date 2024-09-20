<?php

namespace App\Message\Command;

use App\Repository\SubscriptionRepository;
use App\Service\SubscriptionUUID;
class CreateSubscriptionMessage
{
    private SubscriptionUUID $subscriptionUUID;
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionUUID $uuid,SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionUUID = $uuid;
        $this->subscriptionRepository = $subscriptionRepository;

    }
}