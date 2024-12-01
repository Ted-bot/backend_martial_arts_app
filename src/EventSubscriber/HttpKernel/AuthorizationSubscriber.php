<?php

namespace App\EventSubscriber\HttpKernel;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use ApiPlatform\Symfony\EventListener\EventPriorities;

class AuthorizationSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            EventPriorities::PRE_READ => 'onRequest'
        ];
    }

    public function onRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // dd(['got Request' => $request->headers->get('Authorization')]);
        $authorization = $request->headers->get('Authorization');
        // dump($authorization);
        $request->headers->set('X-Authorization', $authorization);
        $request->headers->set('X-TESTER', $authorization);

        // if(isset($authorization)) dd($request);
        // if($authorization){
        //     $breakUpAuthorization = explode(" ",$authorization);
        //     // dd(['got Request' => $request->headers->set('X-Authorization', $breakUpAuthorization[0])]);

        //     if($breakUpAuthorization[0] && $breakUpAuthorization[0] === 'Bearer'){
        //         $modifyAuthorization = trim($breakUpAuthorization[1]);
        //         $request->headers->set('X-Authorization', $modifyAuthorization);
        //         // dd(['got Request' => $request->headers->set('X-Authorization', $modifyAuthorization)]);
        //     }
        // }
        // $request->headers->set('Content-Type', 'application/json');
        // $request->headers->set('Content-Type', 'application/ld+json');
        // $request->headers->set('Access-Control-Allow-Origin', '*');
        // $request->headers->set('Content-Range', '*');
    }

}