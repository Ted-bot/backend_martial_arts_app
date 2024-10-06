<?php 

namespace App\Dto\MollieClient;

use App\Entity\Product;
use App\Service\SubscriptionUUID;
use Symfony\Component\Validator\Constraints as Assert;

class SubscriptionOrderLine 
{
    private SubscriptionUUID $userSubscriptionId;

    public function __construct(
        #[Assert\Type('int')]
        public int $productSubscriptionId,

        #[Assert\Type('int')]
        public string $lengthSubscription,

        #[Assert\Type('string')]
        public string $timeUnitSubscription,
        
        #[Assert\Type('numeric')]
        public string $amountSubscription,
    ){}

        /**
         * Get the value of userSubscriptionId
         */ 
        public function getUserSubscriptionId(): SubscriptionUUID
        {
            // dd(['uuid' => $this->userSubscriptionId]);
            return $this->userSubscriptionId;
        }

        /**
         * Set the value of userSubscriptionId
         *
         * @return  self
         */ 
        public function setUserSubscriptionId(SubscriptionUUID $userSubscriptionId): static
        {
                $this->userSubscriptionId = $userSubscriptionId;

                return $this;
        }

        /**
         * Get the value of productSubscriptionId
         */ 
        public function getProductSubscriptionId(): int
        {
                return $this->productSubscriptionId;
        }

        /**
         * Set the value of productSubscriptionId
         *
         * @return  self
         */ 
        public function setProductSubscription($productSubscriptionId): static
        {
                $this->productSubscriptionId = $productSubscriptionId;

                return $this;
        }

        /**
         * Get the value of lengthSubscription
         */ 
        public function getLengthSubscription(): string
        {
                return $this->lengthSubscription;
        }

        /**
         * Set the value of lengthSubscription
         *
         * @return  self
         */ 
        public function setLengthSubscription($lengthSubscription): static
        {
                $this->lengthSubscription = $lengthSubscription;

                return $this;
        }

        /**
         * Get the value of timeUnitSubscription
         */ 
        public function getTimeUnitSubscription(): string
        {
                return $this->timeUnitSubscription;
        }

        /**
         * Set the value of timeUnitSubscription
         *
         * @return  self
         */ 
        public function setTimeUnitSubscription($timeUnitSubscription): static
        {
                $this->timeUnitSubscription = $timeUnitSubscription;

                return $this;
        }

        /**
         * Get the value of amountSubscription
         */ 
        public function getAmountSubscription(): string
        {
                return $this->amountSubscription;
        }

        /**
         * Set the value of amountSubscription
         *
         * @return  self
         */ 
        public function setAmountSubscription($amountSubscription): static
        {
                $this->amountSubscription = $amountSubscription;

                return $this;
        }
}