<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener {

    // /**
    //  * @var RequestStack
    //  */
    // private $requestStack;
    
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(
        private RequestStack $requestStack,
        private Security $security
    )
    {
        // $this->requestStack = $requestStack;
    }
    
    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        /** @var User $user */
        $user = $this->security->getUser();
        // dd(['user' => $user,'data' => $event->getData(), 'request' => $request]);
        $payload       = $event->getData();
        $payload['ip'] = $request->getClientIp();
        $payload['id'] = $user->getId();
    
        $event->setData($payload);
    
        $header        = $event->getHeader();
        $header['cty'] = 'JWT';
    
        $event->setHeader($header);
    }
}