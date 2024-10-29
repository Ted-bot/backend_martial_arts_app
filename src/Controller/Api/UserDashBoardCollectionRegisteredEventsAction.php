<?php

namespace App\Controller\Api;

use App\Class\Role;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\PostEvent;
use App\ApiResource\UserApi;
use App\Entity\Subscription;
use App\ApiResource\ProductApi;
use App\ApiResource\PostEventApi;
use App\ApiResource\SubscriptionApi;
use App\Service\UserDashBoardHelper;
use App\Enum\MolliePaymentStatusEnum;
use App\Repository\PostEventRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use App\Dto\UserDashboard\UserDashBoardDtoDto;
use Symfony\Component\HttpFoundation\JsonResponse;
// use SerializerIn
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[AsController]
class UserDashBoardCollectionRegisteredEventsAction extends AbstractController
{
    public function __construct(
        private EntityManager $entityManager,
        private UserDashBoardHelper $dashboardHelper,
        private MicroMapperInterface $microMapper,
        private PostEventRepository $postRepo
    ){}

    public function __invoke()
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        /** @var User $user Object */
        $user = $this->getUser();
        
        if(empty($this->postRepo->findUserAllUpcomingSubscribedAndPublishedEvent($user->getId())[0])){
            return ['error' =>'No Subscribed Events Available']; // create DTO
        }

        $getUserAllSubscribedPublishedEvents = $this->postRepo->findUserAllUpcomingSubscribedAndPublishedEvent($user->getId());

        // $dtoAllUserSubscibedPublishedEvents = array_map(function(PostEvent $postEvent) {
        //     return $this->microMapper->map($postEvent, PostEventApi::class, [
        //         MicroMapperInterface::MAX_DEPTH => 1
        //     ]); 
        //     }, $getUserAllSubscribedPublishedEvents
        // );

        // dd('allSubscibedEvents', $getUserAllSubscribedPublishedEvents);

        return $getUserAllSubscribedPublishedEvents;       
    }
}


