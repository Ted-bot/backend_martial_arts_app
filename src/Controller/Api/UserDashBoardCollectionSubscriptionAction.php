<?php

namespace App\Controller\Api;

use App\Class\Role;
use App\Entity\Product;
use App\Entity\PostEvent;
use App\ApiResource\UserApi;
use App\Entity\Subscription;
use App\ApiResource\ProductApi;
use App\ApiResource\SubscriptionApi;
use App\Service\UserDashBoardHelper;
use App\Enum\MolliePaymentStatusEnum;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use App\Dto\UserDashboard\UserDashBoardDtoDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
// use SerializerIn
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[AsController]
class UserDashBoardCollectionSubscriptionAction extends AbstractController
{
    public function __construct(
        private EntityManager $entityManager,
        private UserDashBoardHelper $dashboardHelper,
        private MicroMapperInterface $microMapper
    ){}

    public function __invoke()
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $user = $this->getUser();
        
        if(empty($user->getSubscriptions()?->getValues()[0])){
            return ['error' =>'No Valid Subscription Available']; // create DTO
        }

        /** @var Subscription $subscription Object */
        $subscriptions = $this->entityManager->getRepository(Subscription::class)->findBy(
            ['subscriptionOwnedBy' =>  $user->getId()], 
            ['createdAt' => 'DESC']
        );
        
        if($subscriptions === null){
            return ['error' =>'No Valid Subscription Available']; // create DTO
        }

        $dtoSubscriptions = array_map(function(Subscription $subscription) {
            return $this->microMapper->map($subscription, SubscriptionApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1
            ]); 
            }, $subscriptions
        );

        // dd('dtoSubscription', $dtoSubscriptions);

        return $dtoSubscriptions;        
        // return $subscriptions;        
    }
}


