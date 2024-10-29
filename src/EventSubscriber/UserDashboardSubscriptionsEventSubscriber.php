<?php

namespace App\EventSubscriber;

// ...
use Twig\Environment;
use App\Entity\PostEvent;
use App\Entity\Subscription;
use App\ApiResource\PostEventApi;
use Symfony\Component\Mime\Email;
use Twig\Loader\FilesystemLoader;
use App\Repository\UserRepository;
use App\ApiResource\SubscriptionApi;
use App\Request\ResetPasswordRequest;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface AS EntityManager;
use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
// use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;

final class UserDashboardSubscriptionsEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private JWTAuthenticator $jwtEncoder,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManager $entityManager,
        private UserRepository $userRepository,
        private MicroMapperInterface $microMapper,
        )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelRequest',
        ];
    }

    // public function onKernelRequest(RequestEvent $event)
    public function onKernelRequest(ResponseEvent $event)
    {
        if (!$event->isMainRequest() || !str_starts_with($event->getRequest()->get('_route'), '_api_/user_subscriptions/{email}/dashboard.{_format}_get_collection')) {
            return;
        }

        $userIdentifier = $this->jwtEncoder->authenticate($event->getRequest());

        $user = $this->userRepository->findOneBy(['email' => $userIdentifier->getAttributes()['payload']['username']]);
        

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
                MicroMapperInterface::MAX_DEPTH => 2
            ]); 
            }, $subscriptions
        );

        $event->getResponse()->setContent(json_encode($dtoSubscriptions));

        // dd('checkRsponse',$event);
        return $event;    

        
    }


}