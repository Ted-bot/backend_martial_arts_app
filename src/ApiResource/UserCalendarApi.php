<?php

namespace App\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Api\SubscribeEventActionController;
use App\Controller\Api\UserCalendarActionController;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            // security: 'is_granted("ROLE_ADMIN")',
        ),
        new Get(
            // provider: UserDashboardStateProvider::class,
            // name: 'get_user_data',
            uriTemplate: '/subscribe/{email}/events',
            uriVariables: 'email',
            controller: UserCalendarActionController::class,
            read: false,
            status: 201
            // serialize: false
            // normalizationContext: ['groups' => ['publication']],
        )
        ],
        
)]
class UserCalendarApi
{

}