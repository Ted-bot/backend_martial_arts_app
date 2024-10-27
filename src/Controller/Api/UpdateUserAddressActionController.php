<?php

namespace App\Controller\Api;

use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
use App\Repository\AddressRepository;
use App\Repository\PostaddressRepository;
use App\Repository\UserProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\UserDashboard\UpdateUserAddressDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class UpdateUserAddressActionController extends AbstractController
{
    private $logger;
    // private $managerRegister;
    public function __construct(
        private Security $security,
        private AddressRepository $addressRepo,
        private EntityManager $entityManager,
        private ManagerRegistry $managerRegister,
        private MicroMapperInterface $microMapper,
        LoggerInterface $eventSubscriptionLogger,
    ){
        $this->logger = $eventSubscriptionLogger;
    }

    public function __invoke(#[MapRequestPayload] UpdateUserAddressDto $request)
    {
        $this->denyAccessUnlessGranted(Role::ROLE_USER_STUDENT);

        // dd(['got_requst_update' => $request]);
        $response  = new ResponseDto();
        $addressId = $request->address_id;
            // unit_number
            // street_number
            // address_line
            // postal_code        
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $datetime = new DateTimeImmutable();
        $currentTimeEvent = $datetime->setTimezone(new DateTimeZone('Europe/Amsterdam'));

        // if(!$eventIsNumber) return $response;        
        if(!$this->addressRepo->findOneBy(['id' => $addressId])) return $response;
        
        /**
         * @var mixed
         */
        $updateAddress = $this->addressRepo->findOneBy(['id' => $addressId]);
        if($request->address_line) $updateAddress->setAddressLine($request->address_line);
        if($request->unit_number) $updateAddress->setUnitNumber($request->unit_number);
        if($request->street_number) $updateAddress->setStreetNumber($request->street_number);
        if($request->postal_code) $updateAddress->setPostalCode($request->postal_code);
        if($request->city) $updateAddress->setCity($request->city);
        if($request->state_id) $updateAddress->setLibReactState($request->state_id);
        if($request->city_id) $updateAddress->setLibReactCity($request->city_id);

        $this->entityManager->beginTransaction();
        
        try {            
            // $this->entityManager->persist($manageUpcomingEvent);
            $this->entityManager->flush();
            $this->entityManager->commit();

            $response->status = 201;
            $response->message = "You have Updated your address successfully !";
            return $response;
    
        } catch (Throwable $e) {

            $this->entityManager->rollback();
            $this->managerRegister->resetManager();
            $this->entityManager->flush();
            $this->entityManager->commit();
            
            $this->logger->debug('An error occurred when signin up for a event!', [
                'user' => $currentUser->getId(),
                'time' => $currentTimeEvent,
                'error' => $e->getMessage()
            ]);

            $response->message = 'Excuse use something went wrong from our side.., please try again later';
            return $response;
        }        
    }

}
