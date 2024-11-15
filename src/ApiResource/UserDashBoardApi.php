<?php

namespace App\ApiResource;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Api\UserDashBoardActiveSubscriptionAction;
use App\Controller\Api\UserDashBoardCollectionSubscriptionAction;

#[ApiResource(
    shortName: 'GetOneUserSubscription',
    operations: [
        new GetCollection(
            controller: NotFoundAction::class
        ),
        new GetCollection(
            uriTemplate: '/user_subscriptions/{email}/dashboard.{_format}',
            uriVariables: 'email',
            controller: UserDashBoardCollectionSubscriptionAction::class,
            read: false,
            normalizationContext: ['groups' => ['userdashboard:read']],
        ),
        new Get(
            uriTemplate: '/user_subscription/{email}/dashboard/valid.{_format}',
            uriVariables: 'email',
            controller: UserDashBoardActiveSubscriptionAction::class,
            read: false,
            )
        ],
        
)]
class UserDashBoardApi // implements \Serializable
{
    #[Groups(['userdashboard:read'])]
    #[ApiProperty(identifier:true)]
    public $id;

    #[ApiProperty(readable:true)]
    public $status;
    protected $amount;
    protected $transferId;
    protected $duration ;
    protected $dateStart;
    protected $dateEnd;
    protected $createdAt;
    protected $updatedAt;
    protected $subscribedProduct;
    protected $tokenManager;

//     /**
//      * Serializing the subscription data that is set into the session
//      */
//     /** @see \Serializable::serialize() */
//     public function serialize()
//     {
//         return serialize(array(
//                 $this->id,
//                 $this->status,
//                 $this->amount,
//                 $this->transferId,
//                 $this->duration,
//                 $this->dateStart,
//                 $this->dateEnd,
//                 $this->updatedAt,
//                 $this->subscribedProduct,
//                 $this->tokenManager,
//         ));
//     }

//     /** @see \Serializable::unserialize() */
//     public function unserialize($serialized)
//     {
//         list (
//                 $this->id,
//                 $this->status,
//                 $this->amount,
//                 $this->transferId,
//                 $this->duration,
//                 $this->dateStart,
//                 $this->dateEnd,
//                 $this->updatedAt,
//                 $this->subscribedProduct,
//                 $this->tokenManager,
//                 ) = unserialize($serialized);
//     }

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getTransferId()
    {
        return $this->transferId;
    }

    public function setId()
    {
        return $this->id;
    }

    public function setStatus()
    {
        return $this->status;
    }

    public function setTransferId()
    {
        return $this->transferId;
    }
//     public function setId(int $id): void
// {
//     $this->id = $id;
// }
}