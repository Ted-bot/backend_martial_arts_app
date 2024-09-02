<?php

namespace App\Service;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;


class OrderCalulator
{
    public $result;

    /**
     * Begin calculation with number / float / numeric
     */ 
    public function __construct(int|string $start = 0)
    {
        $this->result = $start;
    }

    /**
     * Add to result
     */ 
    public function add(int|string $value = null)
    {
        $this->result = BigDecimal::of($this->result)->plus($value);
        return $this;
    }

    /**
     * Subtract by result
     */ 
    public function subtract(int|float $value = null)
    {        
        
        $this->result = BigDecimal::of($this->result)->minus($value);
        return $this;
    }

    /**
     * Divide by result
     */ 
    public function divide(int|float $value)
    {
        // dd(['inputDivide' => $this->result, 'value' => $value]);
        $this->result = BigDecimal::ofUnscaledValue($this->result)->dividedBy($value, 2, RoundingMode::HALF_UP);
        return $this;
    }

    /**
     * Multipli by result
     */ 
    public function multipli(int|float $value = null)
    {        
        $this->result = BigDecimal::of($this->result)->multipliedBy($value);
        return $this;
    }

    /**
     * Get the value of result
     */ 
    public function getResult()
    {
        return $this;
    }
    
    /**
     * Get the value of result
     */ 
    public function toString()
    {
        return $this->result->__toString();
    }

    /**
     * Set the value of result
     *
     * @return  self
     */ 
    private function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * clear the value of result
     *
     * @return  self
     */ 
    public function clearResult()
    {
        $this->setResult(0);

        return $this;
    }
}