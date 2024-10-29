<?php

namespace App\Controller\Api;

use App\Entity\StatusTransfer;
use Mollie\Api\MollieApiClient;
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
use App\ApiResource\AddressApi;
use App\ApiResource\PostEventApi;
use App\Dto\Event\CalendarItemDto;
use App\Dto\Main\EventResponseDto;
use App\Enum\MolliePaymentStatusEnum;
use App\Repository\SubscriptionRepository;
use App\Repository\UserProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\UserDashboard\UpdateUserAddressDto;
use App\Dto\UserSubscription\CancellSubscription;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\MollieApiService;

#[AsController]
class CancellUserSubscriptionActionController extends AbstractController
{
    private $logger;
    // private $managerRegister;
    public function __construct(
        private Security $security,
        private SubscriptionRepository $subscriptionRepo,
        private EntityManager $entityManager,
        private ManagerRegistry $managerRegister,
        private MicroMapperInterface $microMapper,
        private MollieApiService $mollieApiService
        // LoggerInterface $eventSubscriptionLogger,
    ){
        // $this->logger = $eventSubscriptionLogger;
    }

    public function __invoke(#[MapRequestPayload] CancellSubscription $request)
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        $response  = new ResponseDto();
        $uuidSub = $request->uuid;
        
        // /** @var User $currentUser */
        $currentUser = $this->getUser();
        
        $datetime = new DateTimeImmutable();
        $currentTimeEvent = $datetime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        
        try {
            $this->entityManager->beginTransaction();
            // if(!$eventIsNumber) return $response;        
            if(!$this->subscriptionRepo->findOneBy(['uuid' => $uuidSub])) return $response;
            
            /** @var Subscription $subscription */
            $subscription = $this->entityManager->getRepository(Subscription::class)
            ->findOneBy(['uuid' => $uuidSub]);
            
            // dd('got uuid', $subscription);
            $subscription->setStatus(MolliePaymentStatusEnum::CANCELLED);
            $subscription->setUpdatedAt();
            $transferId = $subscription->getTransferId();

            $transaction = $this->entityManager->getRepository(StatusTransfer::class)
            ->findOneBy(['transferId' => $transferId]);

            $customerId = $transaction->getCustomer();

        // try {            
            $this->entityManager->persist($subscription);
            $this->entityManager->flush();
            $this->entityManager->commit();
            
            $this->mollieApiService->cancelUserSubscription($customerId);
            
            $response->status = 201;
            $response->message = "You have Updated your address successfully !";
            return $response;
    
        } catch (Throwable $e) {

            $this->entityManager->rollback();
            $this->managerRegister->resetManager();
            $this->entityManager->flush();
            $this->entityManager->commit();
            
            // $this->logger->debug('An error occurred when signin up for a event!', [
            //     'user' => $currentUser->getId(),
            //     'time' => $currentTimeEvent,
            //     'error' => $e->getMessage()
            // ]);

            $response->message = 'Excuse use something went wrong from our side.., please try again later';
            return $response;
        }        
    }

}
