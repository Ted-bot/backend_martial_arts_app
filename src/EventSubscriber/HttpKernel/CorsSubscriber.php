<?php

namespace App\EventSubscriber\HttpKernel;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class CorsSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onResponse'
        ];
    }

    public function onResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        // $response->headers->set('Content-Type', 'application/json');
        // $response->headers->set('Content-Type', 'application/ld+json');
        // $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Range', '*');
    }

}