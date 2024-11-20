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
use App\Dto\Event\CalendarItemDto;
use App\Enum\MolliePaymentStatusEnum;
use App\Repository\PostEventRepository;
use App\Repository\UserProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class UserCalendarActionController extends AbstractController
{
    private $logger;
    // private $managerRegister;
    public function __construct(
        private Security $security,
        private PostEventRepository $eventRepo,
        private EntityManager $entityManager,
        private ManagerRegistry $managerRegister,
        LoggerInterface $eventSubscriptionLogger,
    ){
        $this->logger = $eventSubscriptionLogger;
        // $this->managerRegistry = $managerRegistry;
    }

    // #[Route('/api/subscribe/events', 
    // name: 'api_subscribe_events',
    // methods: 'POST',)]
    public function __invoke(
        Request $request,
        UserProfileRepository $userProfileRepository,
        // LoggerInterface $loggerInt,
    )
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);
        /** @var User $user */
        $user = $this->getUser();

        /** @var Collection<int, PostEvent> $userSubcribedEvents */
        $userSubcribedEvents = $user->getUserProfile()->getSubscribeToEvents();

        /** @var array<int, PostEvent> $userSubcribedEvents */
        $publishedBlackDragonEvents = $this->eventRepo->findBy(['isPublished' => true]);
        
        $responseArray = [];

        foreach($publishedBlackDragonEvents as $publishedEvent){
            $response = new CalendarItemDto();
            $response->id = $publishedEvent->getId();
            $response->description = $publishedEvent->getDescription();
            $response->title = $publishedEvent->getTitle();
            $response->startDate = $publishedEvent->getStartDate();
            $response->endDate = $publishedEvent->getEndDate();
            $response->resource = $publishedEvent->getDescription();

            foreach($userSubcribedEvents as $userScribedEvent){
                if($publishedEvent->getId() === $userScribedEvent->getId()) $response->selectedEvent = $publishedEvent->getId(); 
            }

            $responseArray[] = $response;
        }

        // dd(['userAgenda' => $responseArray]);

        return $responseArray;
    }

}
