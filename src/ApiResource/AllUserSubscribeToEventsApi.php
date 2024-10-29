<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Controller\Api\SubscribeEventActionController;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Api\UserDashBoardCollectionRegisteredEventsAction;
use ApiPlatform\Action\NotFoundAction;

#[ApiResource(
    operations: [
        new GetCollection(
            controller: NotFoundAction::class
        ),
        new GetCollection(
            // provider: UserDashboardStateProvider::class,
            // name: 'get_user_data',
            uriTemplate: '/user/{email}/registered_events/',
            uriVariables: ['email'],
            controller: UserDashBoardCollectionRegisteredEventsAction::class,
            read: false,
            status: 201
            // serialize: false
            )
    ],
    normalizationContext: ['groups' => ['all_user_events:read']],
        
)]
class AllUserSubscribeToEventsApi
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['all_user_events:read'])]
    #[ApiProperty(identifier:true)]
    public ?int $id = null;
}