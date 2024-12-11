<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Api\UpdateUserAddressActionController;

#[ApiResource(
    shortName: 'UserAddressDashboard',
    paginationClientItemsPerPage: true,
    // paginationItemsPerPage: 10,
    // provider: EntityToDtoStateProvider::class,
    // processor: EntityClassDtoStateProcessor::class,
    // normalizationContext: ['groups' =>  ['read_customer']],
    // security: 'is_granted("ROLE_USER_STUDENT")',
    // stateOptions: new Options(entityClass: UserAddress::class),
    operations: [
        new Post(
            security: 'is_granted("ROLE_USER_STUDENT")', 
            uriTemplate: '/user_address_dashboard/{email}/id/{id}',
            uriVariables: ['email', 'id'],
            // uriVariables: [
            //     'id' => new Link(
            //         fromClass: UserAddressApi::class,
            //         toProperty: 'addressUser'
            //     )
            // ],
            controller: UpdateUserAddressActionController::class,
            read: false,
            status: 201
        ),
        new GetCollection(
            security: 'is_granted("ROLE_USER_SIFU")', 
        ),
        new Get(
            security: 'is_granted("ROLE_USER_STUDENT")', 
        ),
        // new Put(),
        new Delete(),
    ],    
)]
class UpdateUserAddressApi 
{}