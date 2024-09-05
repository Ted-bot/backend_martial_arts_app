<?php

namespace App\Service;

use App\Dto\CreateMollieOrderRequest;
use DateTime;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use App\Enum\CurrencyTypeEnum;
use Doctrine\ORM\EntityManager;
use Mollie\Api\MollieApiClient;
use App\Dto\CreateMollieOrderDto;
use App\Enum\SubscriptionTypeEnum;
use Mollie\Api\Types\SequenceType;
use App\Dto\OrderAmountDto;
use App\Dto\OrderAddressDto;
use App\Dto\OrderMetaDataDto;
use App\Dto\OrderSubscriptionDto;

// use OrderAmountDto
// OrderAddressDto
// OrderMetaDataDto
// SequenceType
// OrderSubscriptionDto


class MollieClientHelper
{
    public $em;
    public $mollie;
    public OrderAmountDto $amount;
    public string $description;
    public OrderAddressDto $billingAddress;
    public OrderAddressDto $shippingAddress;
    public OrderMetaDataDto $metadata;
    public string $locale;
    public string $consumerDateOfBirth;
    public string $orderNumber;
    public string $redirectUrl;
    public string $webhookUrl;
    public string $method;
    public bool $subscription = false;
    public BigDecimal $orderTax;
    public BigDecimal $orderTotalProductPrice;
    public array $lines;
    public array $orderLine;
    public string $sequenceType;

    public function __construct()
    // public function __construct($secretKey)
    {
        // $this->em = $entityManager;
        // $mollie = new MollieApiClient();
        $this->orderTax = BigDecimal::zero();
        $this->orderTotalProductPrice = BigDecimal::zero();
        // $secretKey = $this->ge
        // $this->mollie = $mollie->setApiKey('test_hqd6Sq8D72UUKebcT9RnVhkd6k96x2');
        // $this->mollie = $mollie->setApiKey($secretKey);

        // $this->orders = $this->mollie->orders;
    }

    public function setupOrderToPay(CreateMollieOrderDto $request): self
    {

        $this->setAmount($request->amount);
        $this->setBillingAddress($request->billingAddress);
        $this->setShippingAddress($request->shippingAddress);
        $this->setMetadata($request->metadata);
        $this->setDescription($request->description);
        $this->setLocale($request->locale);
        $this->setRedirectUrl($request->redirectUrl);
        $this->setLines($request->lines);
        $this->setWebhookUrl($request->webhookUrl); //'https://e72d-95-96-151-55.ngrok-free.app' . '/api/webhook/MollieDirectPayment'
        $this->setMethod($request->method);
        $this->setSequenceType('first');
        // !$request->sequenceType ? '' : $this->setSequenceType($request->sequenceType);
        // $this->setLines($request->redirectUrl);

        return $this;
    }

    public function getOrderToPay(): array
    {
        $userOrder = [
            "amount" => $this->amount,
            "billingAddress" => $this->billingAddress,
            "shippingAddress" => $this->shippingAddress,
            "metadata" => $this->metadata,
            "description" => $this->description,
            "locale" => $this->locale,
            "redirectUrl" => $this->redirectUrl,
            // "webhookUrl" => 'https://da15-2a02-a210-4bb-7580-9c3a-36c0-765b-2503.ngrok-free.app' . '/api/webhook/MollieDirectPayment',
            "webhookUrl" => $this->webhookUrl . '/api/webhook/MollieDirectPayment',
            "method" => $this->method,
            "lines" => $this->lines,
            "sequenceType" => $this->sequenceType,
            // $this->sequenceType !== '' ? ["sequenceType" => $this->sequenceType] : ''
            // "sequenceType" => $this->sequenceType ? $this->sequenceType : '',
            // SequenceType::SEQUENCETYPE_FIRST
        ];
        // dd(['userOrder' => $userOrder, 'subscription' => $this->subscription]);
        return $userOrder;
        // return !$this->subscription ? $userOrder : array_push($userOrder, $this->sequenceType);
    }

    public function createOrderLine($order, $prodUrlNr, $userProductTax, $exchangeToCountry, $userSelectedProduct): self
    {
        $currentTime = new DateTime();
        $selctedProductTotalPrice = BigDecimal::of($userSelectedProduct->getPrice())->multipliedBy($order->getQty());
        $productVatAmount = BigDecimal::of($userProductTax->getVatAmount())->toScale(2, RoundingMode::UP);
        $taxAmountTimesQty = BigDecimal::of($productVatAmount)->multipliedBy($order->getQty());
        $susbcriptionType = SubscriptionTypeEnum::tryFrom($userSelectedProduct->getDuration()->getValue());
        $isProduct = $susbcriptionType == SubscriptionTypeEnum::UNAVAILABLE;

        $subscriptionDuration = in_array($susbcriptionType->getValue(),['month','UNAVAILABLE']);
        $addMonthOrWeek = $subscriptionDuration !== true ? 'week' : 'month';
        // trailOrProduct: if false set 1 (month duration refund) else set specified duration refund of product
        $trailOrProduct = $subscriptionDuration == false ? 1 : $userSelectedProduct->getDurationLength()->getValue(); /// month or week == true 
        $setDurationProduct = $isProduct ? $trailOrProduct : $userSelectedProduct->getDurationLength()->getValue();

        $orderLine = [
            'sku' => $order->getProduct()->getSku(), // create sku
            // 'type' => 'store_credit', ???
            'description' => $order->getProduct()->getName(),
            'productUrl' => 'http://localhost:5173/product' . $prodUrlNr,
            'imageUrl' => 'http://localhost:5173/testimageUrl',
            'quantity' => $order->getQty(),
            'vatRate' => $userProductTax->getVatRate()->getProcent(),
            'unitPrice' => [
                'currency' => $exchangeToCountry ?: CurrencyTypeEnum::EUR,
                'value' => $order->getProduct()->getPrice()
            ],
            'totalAmount' => [
                'currency' => $order->getProduct()->getCurrencyType()->getId(),
                'value' => $selctedProductTotalPrice
            ],
            'vatAmount' => [
                'currency' => $exchangeToCountry ?: CurrencyTypeEnum::EUR,
                'value' => $taxAmountTimesQty,
            ],
            'productDetails' => [
                'totalProductCalculations' => [
                    'totalProductPrice' => $order->getPrice(),
                    'totalTaxPrice' => $taxAmountTimesQty,
                ],
                'subscriptionDetails' => [
                    'productSubscription' => $susbcriptionType->getValue(),
                    'subscriptionAmount' => $userSelectedProduct->getDurationLength()->getValue() > 1 ? BigDecimal::of($order->getPrice())->dividedBy($userSelectedProduct->getDurationLength()->getValue(), 2, RoundingMode::UP)->__toString() : '',
                    'subscriptionLength' => $userSelectedProduct->getDurationLength(),
                    'productSubscriptionStart' => $currentTime->format('Y-m-d'),
                    'productSubscriptionEnd' => $currentTime->modify('+' . $setDurationProduct . ' ' . $addMonthOrWeek)->format('Y-m-d')
                ]
            ]
        ];

        $this->orderTax->plus($orderLine['productDetails']['totalProductCalculations']['totalTaxPrice']);
        $this->orderTotalProductPrice->plus($orderLine['productDetails']['totalProductCalculations']['totalProductPrice']);

        $this->lines[] = $orderLine;
        return $this;
    }

    public function createOrder(array $array)
    {
        return $this->mollie->orders->create($array);
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
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

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
      * Get the value of shippingAddress
      */ 
      public function getShippingAddress()
      {
          return $this->shippingAddress;
      }
  
      /**
       * Set the value of shippingAddress
       *
       * @return  self
       */ 
      public function setShippingAddress($shippingAddress)
      {
          $this->shippingAddress = $shippingAddress;
  
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

    /**
     * Get the value of orderTax
     */ 
    public function getOrderTax()
    {
        return $this->orderTax;
    }

    /**
     * Set the value of orderTax
     *
     * @return  self
     */ 
    public function setOrderTax($orderTax)
    {
        $this->orderTax = $orderTax;

        return $this;
    }

    /**
     * Get the value of orderTotalProductPrice
     */ 
    public function getOrderTotalProductPrice()
    {
        return $this->orderTotalProductPrice;
    }

    /**
     * Set the value of orderTotalProductPrice
     
     *
     * @return  self
     */ 
    public function setOrderTotalProductPrice($orderTotalProductPrice)
    {
        $this->orderTotalProductPrice = $orderTotalProductPrice;

        return $this;
    }


    /**
     * Get the value of sequenceType
     */ 
    public function getSequenceType(): SequenceType|string
    {
        return $this->sequenceType;
    }

    /**
     * Set the value of sequenceType
     *
     * @return  self
     */ 
    public function setSequenceType(SequenceType|string $sequenceType)
    {
        $this->sequenceType = $sequenceType;
        $this->setSubscription(true);

        return $this;
    }

    /**
     * Get the value of setSubscription
     */ 
    public function getSubscription(): bool
    {
        return $this->subscription;
    }

    /**
     * Set the value of setSubscription
     *
     * @return  self
     */ 
    public function setSubscription(bool $value)
    {
        $this->subscription = $value;

        return $this;
    }
}