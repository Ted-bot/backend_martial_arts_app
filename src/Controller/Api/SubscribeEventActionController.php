<?php

namespace App\Controller\Api;

use Throwable;
use DateTimeZone;
use App\Class\Role;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\PostEvent;
use Brick\Math\BigDecimal;
use App\Entity\UserProfile;
use App\Entity\Subscription;
use App\Entity\TokenManager;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use App\Dto\Main\ResponseDto;
use App\ApiResource\PostEventApi;
use App\Dto\Event\CalendarItemDto;
use App\Dto\Main\EventResponseDto;
use App\Enum\MolliePaymentStatusEnum;
use App\Repository\PostEventRepository;
use App\Repository\UserProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class SubscribeEventActionController extends AbstractController
{
    private $logger;
    // private $managerRegister;
    public function __construct(
        private Security $security,
        private PostEventRepository $eventRepo,
        private EntityManager $entityManager,
        private ManagerRegistry $managerRegister,
        private MicroMapperInterface $microMapper,
        LoggerInterface $eventSubscriptionLogger,
    ){
        $this->logger = $eventSubscriptionLogger;
    }

    public function __invoke(
        Request $request,
    )
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $eventId = $request->getPayload()->get('event_id');        
        $addOrRemoveEvent = $request->getPayload()->get('select');  
        $eventIsNumber = is_numeric($eventId);
        $response  = new ResponseDto();

        if(!$eventIsNumber) return $response;        
        if(!$this->eventRepo->findOneBy(['id' => $eventId])) return $response;
        
        $findEvent = $this->eventRepo->findOneBy(['id' => $eventId]);
        $timeEvent = date_format($findEvent->getStartDate(),'d-M H:m');
        $datetime = new DateTimeImmutable();
        $currentTimeEvent = $datetime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        
        if(!$findEvent->isPublished()) return $response;         
        if($currentTimeEvent > $findEvent->getStartDate()) return $response; 

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        /** @var Subscription $subscription Object */
        $subscription = $this->entityManager->getRepository(Subscription::class)->findOneBy(
            ['subscriptionOwnedBy' =>  $currentUser->getId(), 'status' => MolliePaymentStatusEnum::PAID], 
            ['createdAt' => 'DESC']
        );

        if(!$subscription) new ResponseDto(message: 'No valid Subscription');
        
        if(!$subscription?->getTokenManager()) throw new \Exception('no avaivailabe tokens', 401);
        
        /** @var TokenManager $updateSubTokenManger */
        $userSubscriptionTokenManager = $subscription?->getTokenManager();

        // check TokenManager 

        $userCurrentTokens = (int) $userSubscriptionTokenManager->getTokens();

        /** @var UserProfile $userProfile */
        $userProfile = $currentUser->getUserProfile();

        if($addOrRemoveEvent >= 2) return new ResponseDto(message: "Security: could not handle select option!");
        
        /** @var PostEvent $userSelectedEvent */
        $userSelectedEvent = $this->entityManager->getRepository(PostEvent::class)
        ->getArrayPublishedAndUserSubscribedEventIds($currentUser->getId(), $eventId);
        
        if(!!$addOrRemoveEvent){
            if($userCurrentTokens === 0) return new ResponseDto('Not enough Tokens');
            if($userSelectedEvent) return new ResponseDto(message: "You have all ready Signed up for {$timeEvent}!");
            
            $updateTokens = BigDecimal::of($userCurrentTokens)->minus(10)->__tostring();            
            $manageUpcomingEvent = $findEvent->addSubscribe($userProfile);
        } else {           
            if(!$userSelectedEvent) return new ResponseDto(message:"You have already unsubscribed from {$timeEvent}!"); 

            $updateTokens = BigDecimal::of($userCurrentTokens)->plus(10)->__tostring();
            $manageUpcomingEvent = $findEvent->removeSubscribe($userProfile);
        }
        
        $userSubscriptionTokenManager->setTokens((int) $updateTokens);    
        $responseMessage = $addOrRemoveEvent ? 'assigned to' : 'unscubsribed from';
        $responseEndMessage = $addOrRemoveEvent ? 'Cant wait to se you there' : 'We hope to see you another time!';

        $this->entityManager->getConnection()->setAutoCommit(false);
        $this->entityManager->beginTransaction();
        
        try {            
            $this->entityManager->persist($manageUpcomingEvent);
            $this->entityManager->flush();
            $this->entityManager->commit();

            $response->status = 201;
            $response->message = "You have {$responseMessage} {$findEvent->title} \n on {$timeEvent}, {$responseEndMessage} !";
            return $response;
    
        } catch (Throwable $e) {

            $this->entityManager->rollback();
            $this->managerRegister->resetManager();
            $this->entityManager->flush();
            $this->entityManager->commit();
            
            $this->logger->debug('An error occurred when signin up for a event!', [
                'user' => $currentUser->getId(),
                'profile' => $userProfile->getId(),
                'time' => $currentTimeEvent,
                'error' => $e->getMessage()
            ]);

            // $response->message = 'Excuse us something went wrong from our side.., please try again later';
            // return $response;
            return new JsonResponse(['message' => $e->getMessage()],Response::HTTP_EXPECTATION_FAILED);
        }        
    }

}
