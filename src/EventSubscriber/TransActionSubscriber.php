<?php

namespace App\EventSubscriber;

use App\Event\TransActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Repository\StatusTransferRepository;
use App\Entity\StatusTransfer;

class TransActionSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            TransActionEvent::CREATE => 'onCreateTransAction',
            TransActionEvent::READ => 'onReadTransAction',
        ];
    }

    public function onCreateTransAction(TransActionEvent $transActionEvent)
    {
        $entity = $transActionEvent->getTransAction();

         
        dd($transActionEvent);
    }

    public function onReadTransAction(TransActionEvent $transActionEvent)
    {
        dd($transActionEvent);
    }
    
}