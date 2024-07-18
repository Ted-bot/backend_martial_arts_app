<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Mollie\Api\MollieApiClient;


class MollieClient
{
    private $em;
    private $mollie;

    public function __construct($secretKey)
    {
        // $this->em = $entityManager;
        $mollie = new MollieApiClient();
        // $secretKey = $this->ge
        // $this->mollie = $mollie->setApiKey('test_hqd6Sq8D72UUKebcT9RnVhkd6k96x2');
        $this->mollie = $mollie->setApiKey($secretKey);
    }

}