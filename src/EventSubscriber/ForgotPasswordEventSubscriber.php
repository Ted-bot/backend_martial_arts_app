<?php

namespace App\EventSubscriber;

// ...
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use Symfony\Component\Mime\Email;
use Twig\Loader\FilesystemLoader;
use App\Request\ResetPasswordRequest;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface AS EntityManager;
use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

final class ForgotPasswordEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private JWTEncoderInterface $jwtEncoder,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManager $entityManager,
        private UserRepository $userRepository,
        )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            CreateTokenEvent::class => 'onCreateToken',
            UpdatePasswordEvent::class => 'onUpdatePassword',
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || !str_starts_with($event->getRequest()->get('_route'), 'coop_tilleuls_forgot_password')) {
            return;
        }

        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );

        $token = $extractor->extract($event->getRequest());

        if($token != false) {
            throw new AuthenticationException('Authentication attempt!');
        }
    }


    public function onCreateToken(CreateTokenEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();

        $message = (new Email())
            ->from('AmsterdamMartialArtst@example.com')
            ->to($user->getEmail())
            // ->cc('tkbotch@gmail.com')
            ->subject('Reset your password')
            ->html($this->twig->render(
                'reset_mail.html.twig',
                [
                    'reset_password_url' => sprintf('https://www.example.com/forgot-password/%s', $passwordToken->getToken()),
                ]
            ));

        $this->mailer->send($message);
    }

    public function onUpdatePassword(UpdatePasswordEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();
        $userNewPassword = $event->getPassword();

        // var_dump(['userPassword' => $userNewPassword]);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $userNewPassword
        );

        $this->userRepository->upgradePassword($user, $hashedPassword);
        // $this->entityManager->persist($user); // persist only when creating new entity
        $this->entityManager->flush();
    }

    public function onKernelException(ExceptionEvent $event): JsonResponse
    {
        // Get the exception object from the received event
        $exception = $event->getThrowable();
        // Handle the exception or modify the response based on its type
        // if ($exception instanceof SpecificExceptionType) {
        //     return new JsonResponse([''=> $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST); 
        // }

        return new JsonResponse([''=> $exception->getMessage()], Response::HTTP_BAD_REQUEST);
    }

    
}