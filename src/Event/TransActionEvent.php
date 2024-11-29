<?php

namespace App\Event;

use App\Entity\StatusTransfer;

class TransActionEvent
{
    private StatusTransfer $transaction;
    const CREATE = "transaction.created";
    const READ = "transaction.read";
    const UPDATE = "transaction.update";
    const DELETE = "transaction.delete";
    public function __construct(StatusTransfer $transaction)
    {
        $this->transaction = $transaction;
    }

    public function getTransAction()
    {
        return $this->transaction;
    }

    public function getId()
    {
        return $this->transaction->getId();
    }

    public function getStatus()
    {
        return $this->transaction->getStatus();
    }

    public function getTransferId()
    {
        return $this->transaction->getTransferId();
    }
    
    public function getCustomer()
    {
        return $this->transaction->getCustomer();
    }

    public function getCreatedAt()
    {
        return $this->transaction->getCreatedAt();
    }

    public function getUserOrder()
    {
        return $this->transaction->getUserOrder();
    }
}