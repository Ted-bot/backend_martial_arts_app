<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
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
            security: 'is_granted("ROLE_USER_STUDENT")', 
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
        new Get(
            security: 'is_granted("ROLE_USER_STUDENT")', 
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_STUDENT")', 
        ),
    ],    
)]
class CancellUserSubscriptionApi 
{}