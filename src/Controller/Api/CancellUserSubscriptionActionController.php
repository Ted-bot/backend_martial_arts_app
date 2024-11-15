<?php

namespace App\Controller\Api;

use Throwable;
use DateTimeZone;
use App\Class\Role;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Subscription;
use App\Entity\TokenManager;
use Psr\Log\LoggerInterface;
use App\Dto\Main\ResponseDto;
use App\Entity\StatusTransfer;
use App\Service\MollieApiService;
use App\Enum\MolliePaymentStatusEnum;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Dto\UserSubscription\CancellSubscription;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        
        $datetime = new DateTimeImmutable();
        $currentTimeEvent = $datetime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        
        try {
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->setAutoCommit(false);
            // $this->entityManager->wrapInTransaction();
            $this->entityManager->commit();
            if(!$this->subscriptionRepo->findOneBy(['uuid' => $uuidSub])) return $response;
            
            /** @var Subscription $subscription */
            $subscription = $this->entityManager->getRepository(Subscription::class)
            ->findOneBy(['uuid' => $uuidSub]);
            
            $subscription->setStatus(MolliePaymentStatusEnum::CANCELLED);
            $subscription->setUpdatedAt();
            $transferId = $subscription->getTransferId();

            $transaction = $this->entityManager->getRepository(StatusTransfer::class)
            ->findOneBy(['transferId' => $transferId]);

            $customerId = $transaction->getCustomer();

        // try {            
            // $this->entityManager->persist($subscription);
            $this->entityManager->flush();
            $this->entityManager->commit();
            
            $cancellSubscription = $this->mollieApiService->cancelUserSubscription($customerId, $transferId);
            
            $response->status = 201;
            $response->message = "You have Updated your address successfully !";
            $response->body = $cancellSubscription;
            // $response->message = "You have Updated your address successfully !";
            // $response->message = "You have Updated your address successfully !";
            return $response;
    
        } catch (Throwable $e) {

            // $this->entityManager->isTransactionActive();
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
