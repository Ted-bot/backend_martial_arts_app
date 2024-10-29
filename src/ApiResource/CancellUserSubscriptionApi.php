<?php

namespace App\ApiResource;

use App\Entity\UserAddress;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\ApiResource\ShopOrderApi;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\EntityToDtoStateProvider;
use App\Mapper\AddressApiToEntityMapper;
use App\Mapper\AddressEntityToApiMapper;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\State\EntityClassDtoStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\State\UserAdressEntityToDtoStateProvider;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use App\Controller\Api\UpdateUserAddressActionController;
use App\Controller\Api\CancellUserSubscriptionActionController;

#[ApiResource(
    shortName: 'CancellUserSubscription',
    // provider: EntityToDtoStateProvider::class,
    // processor: EntityClassDtoStateProcessor::class,
    // paginationItemsPerPage: 10,
    // normalizationContext: ['groups' =>  ['read_customer']],
    // security: 'is_granted("ROLE_USER_STUDENT")',
    // stateOptions: new Options(entityClass: UserAddress::class),
    operations: [
        new Post(
            uriTemplate: '/cancel_user_subscription/{email}/id/{id}',
            uriVariables: ['email', 'id'],
            // uriVariables: [
            //     'id' => new Link(
            //         fromClass: UserAddressApi::class,
            //         toProperty: 'addressUser'
            //     )
            // ],
            controller: CancellUserSubscriptionActionController::class,
            read: false,
            status: 201
        ),
        new GetCollection(),
        new Get(),
        new Patch(),
    ],    
)]
class CancellUserSubscriptionApi 
{}