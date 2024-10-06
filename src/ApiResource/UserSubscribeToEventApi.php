<?php

namespace App\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Api\SubscribeEventActionController;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            // provider: UserDashboardStateProvider::class,
            // name: 'get_user_data',
            uriTemplate: '/subscribe/events',
            // uriVariables: 'email',
            controller: SubscribeEventActionController::class,
            read: false,
            status: 201
            // serialize: false
            // normalizationContext: ['groups' => ['publication']],
        )
        ],
        
)]
class UserSubscribeToEventApi
{

}