<?php

namespace App\Service;

use DateTime;
use App\Entity\Product;
use Brick\Math\BigDecimal;
use App\Entity\Subscription;
use Brick\Math\RoundingMode;
use App\Enum\CategoryTypeEnum;
use App\Enum\CurrencyTypeEnum;
use App\Service\SubscriptionUUID;
use App\Enum\SubscriptionTypeEnum;
use Mollie\Api\Types\SequenceType;
use App\Enum\MolliePaymentStatusEnum;
use App\Repository\ProductRepository;
use App\Enum\SubscriptionLengthTypeEnum;
use App\Dto\MollieClient\OrderAmountDto;
use App\Dto\MollieClient\OrderAddressDto;
use App\Dto\MollieClient\OrderMetaDataDto;
use App\Dto\MollieClient\CreateMollieOrderDto;
use App\Dto\MollieClient\OrderSubscriptionDto;
use App\Dto\MollieClient\SubscriptionOrderLine;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MollieClientHelper extends AbstractController
{
    /** @var array<int, SubscriptionOrderLine> */
    public array $userSubscriptionOrderLines;
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

    public function __construct(private ProductRepository $productRepo, private EntityManager $entityManager)
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
        foreach($request->lines as $line){           
            
            // set each line in a this helper class as a property 
            // and execute function after paymentId is created
            
            // $productUrl = $line->productUrl; // test with "sku" untill ["slug"] created
            $productSku = $line["sku"]; 
            // dump($productUrl . ' temperarly set as sku! ');
            
            // strip product url and get slug to check if product exist and has subscription
            
            /** @var Product */
            $product = $this->productRepo->findOneBy(['sku' => $productSku]);
            
            if($product === null){
                continue;
            }
            
            if($product->getCategory() !== CategoryTypeEnum::SUB){
                continue;
            }

            $productSubscription = new SubscriptionOrderLine(
                $product->getId(),
                $request->subscriptionDetail->subscriptionLength,
                $request->subscriptionDetail->subscriptionTimeUnit,
                $request->subscriptionDetail->subscriptionAmount
            );
            
            $this->setUserSubscriptionOrderLines($productSubscription);
            
            // When PaymentId is available Execute creating Subscription(s)
            // $this->createUserSubscription();
        }

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

    public function createUserSubscription(string $tranferId)
    {
        foreach($this->getUserSubscriptionOrderLines() as $subscribeUserToProduct)
        {
            try {
                /** @var SubscriptionOrderLine $requestCreateSubscription */
                $requestCreateSubscription = $subscribeUserToProduct;

                $productId = $requestCreateSubscription->getProductSubscriptionId();

                /** @var  Product $product */
                $product = $this->entityManager->getRepository(Product::class)
                    ->findOneBy(['id' => $productId]);
    
                // dd(['product' => $product]);
                $subscriptionLength = $requestCreateSubscription->getLengthSubscription();
                $subscriptionMonthOrWeek = $requestCreateSubscription->getTimeUnitSubscription();
                $subscriptionAmount = $requestCreateSubscription->getAmountSubscription();
                
                $subscriptionId = new SubscriptionUUID();
                $subscription = new Subscription();
                
                $requestCreateSubscription->setUserSubscriptionId($subscriptionId);
                
                $subscriptionLengthConvertToEnum = SubscriptionLengthTypeEnum::from($subscriptionLength);
                $subscription->setSubscribedProduct($product);
                $subscription->setUuid($subscriptionId->create());
                $subscription->setSubscriptionOwnedBy($this->getUser());
                $subscription->setStatus(MolliePaymentStatusEnum::OPEN);
                // $subscription->setTransferId($tranferId); // paymentID will be sent after first payment set transfer Id After paymendId is created
                $subscription->setAmount($subscriptionAmount);
                $subscription->setDuration($subscriptionLengthConvertToEnum); //SubscriptionLengthTypeEnum
                $subscription->setDateEnd('+' . $subscriptionLength . ' ' . $subscriptionMonthOrWeek); //SubscriptionLengthTypeEnum    
                $subscription->setUpdatedAt();
    
                $this->entityManager->persist($subscription);
                $this->entityManager->flush();

            } catch(UniqueConstraintViolationException $e){

                // log error an internal issue has occurred
                dd(['error creating subscripton' => $e]);

            }
        }
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
            "webhookUrl" => $this->webhookUrl . '/api/webhook/MollieDirectPayment',
            "method" => $this->method,
            "lines" => $this->lines,
        ];

        if($this->subscription) $userOrder['sequenceType'] = SequenceType::SEQUENCETYPE_FIRST;
        // if($this->subscription) $userOrder['sequenceType'] = SequenceType::SEQUENCETYPE_RECURRING; 

        return $userOrder;
    }

    public function createOrderLine($order, $prodUrlNr, $userProductTax, $exchangeToCountry, $userSelectedProduct): self
    {

        $currentTime = new DateTime();
        $selctedProductTotalPrice = BigDecimal::of($userSelectedProduct->getPrice())->multipliedBy($order->getQty());
        $productVatAmount = BigDecimal::of($userProductTax->getVatAmount())->toScale(2, RoundingMode::UP);
        $taxAmountTimesQty = BigDecimal::of($productVatAmount)->multipliedBy($order->getQty());
        $susbcriptionType = SubscriptionTypeEnum::tryFrom($userSelectedProduct->getDuration()->getValue());
        $isProductOrSubscription = $susbcriptionType === SubscriptionTypeEnum::UNAVAILABLE;

        $subscriptionDuration = in_array($susbcriptionType->getValue(),['month','unavailable']);
        $addMonthOrWeek = $subscriptionDuration !== true ? 'week' : 'month';
        // trailOrProduct: if false set 1 (month duration refund) else set specified duration refund of product
        $trailOrProduct = $subscriptionDuration == false ? 1 : $userSelectedProduct->getDurationLength()->getValue(); /// month or week == true 
        $setDurationProduct = $isProductOrSubscription ? $trailOrProduct : $userSelectedProduct->getDurationLength()->getValue();
        $unitPrice = $order->getProduct()->getPrice();
        
        if(isset($isProductOrSubscription)){
            $subDuration = $userSelectedProduct->getDurationLength()->getValue();
            $selctedProductTotalPrice = BigDecimal::of($userSelectedProduct->getPrice())->dividedBy($subDuration)->multipliedBy($order->getQty());
            $productVatAmount = BigDecimal::of($userProductTax->getVatAmount())->dividedBy($subDuration,2, RoundingMode::UP);
            $taxAmountTimesQty = BigDecimal::of($productVatAmount)->multipliedBy($order->getQty());
            $unitPrice = BigDecimal::of($order->getProduct()->getPrice())->dividedBy($subDuration);
        }

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
                'value' => $unitPrice
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
            ]
        ];

        if(isset($isProductOrSubscription)){
            $orderLine['productDetails']['subscriptionDetails'] = [
                'subscriptionTimeUnit' => $susbcriptionType->getValue(),
                'subscriptionAmount' => $userSelectedProduct->getDurationLength()->getValue() > 1 ? BigDecimal::of($order->getPrice())->dividedBy($userSelectedProduct->getDurationLength()->getValue(), 2, RoundingMode::UP)->__toString() : '',
                'subscriptionLength' => $userSelectedProduct->getDurationLength(),
                'productSubscriptionStart' => $currentTime->format('Y-m-d'),
                'productSubscriptionEnd' => $currentTime->modify('+' . $setDurationProduct . ' ' . $addMonthOrWeek)->format('Y-m-d')
            ];
        }
        $subscriptionAmount = $orderLine['productDetails']['subscriptionDetails']['subscriptionAmount'];
        $productAmount = $orderLine['productDetails']['totalProductCalculations']['totalProductPrice'];
        
        // $newOrdersTotalProductPrice = BigDecimal::of($this->getOrderTotalProductPrice())->plus($addSubscriptionOrProductPrice);
        // $this->orderTax->plus($orderLine['productDetails']['totalProductCalculations']['totalTaxPrice']);
        $this->setOrderTax($taxAmountTimesQty);
        $addSubscriptionOrProductPrice = $subscriptionAmount ?? $productAmount;

        $newOrdersTotalProductPrice = BigDecimal::of($this->getOrderTotalProductPrice())->plus($addSubscriptionOrProductPrice);
        $this->setOrderTotalProductPrice($newOrdersTotalProductPrice);

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
        $newValue = BigDecimal::of($this->orderTax)->plus($orderTax);
        $this->orderTax = $newValue;

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
        $newValue = BigDecimal::of($this->orderTotalProductPrice)->plus($orderTotalProductPrice);
        $this->orderTotalProductPrice = $newValue;

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

    /**
     * Get the value of userSubscriptionOrderLines
     */ 
    public function getUserSubscriptionOrderLines()
    {
        return $this->userSubscriptionOrderLines;
    }

    /**
     * Set the value of userSubscriptionOrderLines
     *
     * @return  self
     */ 
    public function setUserSubscriptionOrderLines(SubscriptionOrderLine $userSubscriptionOrderLines)
    {
        $this->userSubscriptionOrderLines[] = $userSubscriptionOrderLines;

        return $this;
    }
}