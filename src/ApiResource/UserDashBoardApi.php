<?php

namespace App\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Api\UserDashBoardAction;
use App\State\UserDashboardStateProvider;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            // security: 'is_granted("ROLE_ADMIN")',
        ),
        new Get(
            // provider: UserDashboardStateProvider::class,
            // name: 'get_user_data',
            uriTemplate: '/user/{email}/dashboard',
            uriVariables: 'email',
            controller: UserDashBoardAction::class,
            read: false,
            // serialize: false
            // normalizationContext: ['groups' => ['publication']],
        )
        ],
        
)]
class UserDashBoardApi
{

}