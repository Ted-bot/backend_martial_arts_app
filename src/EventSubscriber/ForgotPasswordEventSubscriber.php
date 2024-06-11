<?php

namespace App\EventSubscriber;

// ...
use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

final class ForgotPasswordEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer, private readonly Environment $twig,private JWTEncoderInterface $jwtEncoder,)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            CreateTokenEvent::class => 'onCreateToken',
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
        dd($event);

        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();

        $message = (new Email())
            ->from('AmsterdamMartialArtst@example.com')
            // ->to($user->getEmail())
            ->to('tkbotch@gmail.com')
            ->cc('tkbotch@gmail.com')
            ->subject('Reset your password')
            ->html($this->twig->render(
                'App:ResetPassword:mail.html.twig',
                [
                    'reset_password_url' => sprintf('https://www.example.com/forgot-password/%s', $passwordToken->getToken()),
                ]
            ));
        $this->mailer->send($message);
        
        dd('message is send');
    }

    
}