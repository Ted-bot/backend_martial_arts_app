<?php

namespace App\Message;
use App\Class\Component\MolliePaymentMessageComponent as MessageComponent;

class SendWebhookMessage 
{
    // private $mollieDirectPaymentWebhook;
    public function __construct(private MessageComponent $mollieDirectPaymentWebhook)
    {    }

    public function getOrderId(): string
    {
        return $this->mollieDirectPaymentWebhook->getId();
    }
}