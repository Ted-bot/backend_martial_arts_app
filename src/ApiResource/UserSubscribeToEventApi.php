<?php

namespace App\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Controller\Api\SubscribeEventActionController;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Api\UsersSubscribedToEventActionController;

#[ApiResource(
    operations: [
        new GetCollection( // delete this not working
            // security: 'is_granted("ROLE_USER_SIFU")', //
            paginationItemsPerPage: 10,
            uriTemplate: '/users_subscribed_to_events/{id}{._format}',
            parameters: ['page' => new QueryParameter, 'subscribedTo' => new QueryParameter],
            controller: UsersSubscribedToEventActionController::class,
            read: false,
            status: 201
        ),
        new Post(
            // provider: UserDashboardStateProvider::class,
            // name: 'get_user_data',
            uriTemplate: '/subscribe/{email}/event/{id}',
            uriVariables: ['email', 'id'],
            controller: SubscribeEventActionController::class,
            read: false,
            status: 201
            // serialize: false
            // normalizationContext: ['groups' => ['publication']],
        )
        ],
        
)]
#[QueryParameter(key: 'q', property: 'freetextQuery')]
class UserSubscribeToEventApi
{

}