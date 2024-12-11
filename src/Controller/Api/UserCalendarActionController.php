<?php

namespace App\Controller\Api;

use DateTimeZone;
use App\Class\Role;
use App\Entity\User;
use App\Entity\PostEvent;
use Psr\Log\LoggerInterface;
use App\Dto\Event\CalendarItemDto;
use App\Repository\PostEventRepository;
use App\Repository\UserProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
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

        /** @var $publishedEvent PostEvent */
        foreach($publishedBlackDragonEvents as $publishedEvent){
            if($publishedEvent instanceof PostEvent) {
                
                $date_gmt = clone $publishedEvent->getStartDate();
                $date_gmt->setTimezone(new DateTimeZone('Europe/Amsterdam'));

                $response = new CalendarItemDto();
                $response->id = $publishedEvent->getId();
                $response->description = $publishedEvent->getDescription();
                $response->title = $publishedEvent->getTitle();
                $response->startDate = $date_gmt;
                $response->endDate = $publishedEvent->getEndDate();
                $response->resource = $publishedEvent->getDescription();

                foreach($userSubcribedEvents as $userScribedEvent){
                    if($publishedEvent->getId() === $userScribedEvent->getId()) $response->selectedEvent = $publishedEvent->getId(); 
                }
                $responseArray[] = $response;
            }
        }

        // dd(['userAgenda' => $responseArray]);

        return $responseArray;
    }

}
