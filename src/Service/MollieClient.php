<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Mollie\Api\MollieApiClient;


class MollieClient
{
    private $em;
    private $mollie;
    private array $amount;
    private array $billingAddress;
    private array $shippingAddress;
    private string $metadata;
    private string $locale;
    private string $consumerDateOfBirth;
    private string $orderNumber;
    private string $redirectUrl;
    private string $webhookUrl;
    private string $method;
    private array $lines;

    public function __construct($secretKey)
    {
        // $this->em = $entityManager;
        $mollie = new MollieApiClient();
        // $secretKey = $this->ge
        // $this->mollie = $mollie->setApiKey('test_hqd6Sq8D72UUKebcT9RnVhkd6k96x2');
        $this->mollie = $mollie->setApiKey($secretKey);

        $this->orders = $this->mollie->orders;
    }

    public function createOrder(array $array)
    {
        return $this->orders->create($array);
    }


    /**
     * Get the value of amount
     */ 
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */ 
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of billingAddress
     */ 
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set the value of billingAddress
     *
     * @return  self
     */ 
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get the value of orderNumber
     */ 
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set the value of orderNumber
     *
     * @return  self
     */ 
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get the value of consumerDateOfBirth
     */ 
    public function getConsumerDateOfBirth()
    {
        return $this->consumerDateOfBirth;
    }

    /**
     * Set the value of consumerDateOfBirth
     *
     * @return  self
     */ 
    public function setConsumerDateOfBirth($consumerDateOfBirth)
    {
        $this->consumerDateOfBirth = $consumerDateOfBirth;

        return $this;
    }

    /**
     * Get the value of locale
     */ 
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the value of locale
     *
     * @return  self
     */ 
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the value of metadata
     */ 
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set the value of metadata
     *
     * @return  self
     */ 
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get the value of redirectUrl
     */ 
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set the value of redirectUrl
     *
     * @return  self
     */ 
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * Get the value of webhookUrl
     */ 
    public function getWebhookUrl()
    {
        return $this->webhookUrl;
    }

    /**
     * Set the value of webhookUrl
     *
     * @return  self
     */ 
    public function setWebhookUrl($webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;

        return $this;
    }

    /**
     * Get the value of method
     */ 
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the value of method
     *
     * @return  self
     */ 
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get the value of lines
     */ 
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Set the value of lines
     *
     * @return  self
     */ 
    public function setLines($lines)
    {
        $this->lines = $lines;

        return $this;
    }
}