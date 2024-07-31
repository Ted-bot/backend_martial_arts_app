<?php

namespace App\ApiBundle\Utilities;


use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver;
// use
class ApiRequestParamConverter 
{
    /*
     * @var SerializerBuilder
     */
    private SerializerInterface $serializer;

    /*
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;
}