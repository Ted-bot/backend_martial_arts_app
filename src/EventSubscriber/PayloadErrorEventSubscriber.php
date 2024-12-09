<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
// use ExceptionInterface

// note:delete this already created listener for api-platform V4
class PayloadErrorEventSubscriber implements EventSubscriberInterface 
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }

    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $isHttpEvent = $event->getThrowable() instanceof HttpExceptionInterface || $event->getThrowable() instanceof ResourceNotFoundException;
        $isValidationEvent = $event->getThrowable()->getPrevious() instanceof ValidationFailedException || $event->getThrowable()->getPrevious() instanceof ResourceNotFoundException;

        // We are only interested in validation errors in an httpException context
        if (!$isHttpEvent || !$isValidationEvent) {
            return;
        }

        if($event->getThrowable()->getPrevious() instanceof ResourceNotFoundException){
            $event->setResponse(new JsonResponse(['message' => $event->getThrowable()->getPrevious()->getMessage()], Response::HTTP_NOT_FOUND));
        }

        if($event->getThrowable()->getPrevious() instanceof HttpExceptionInterface)
        {
            /**
             * @var ValidationFailedException $validationException
             */
            $validationException = $event->getThrowable()->getPrevious();
            $errorMessages = [];
            foreach ($validationException->getViolations() as $violation) {
                $errorMessages[$violation->getPropertyPath()] = $violation->getMessage();
            }
    
            $event->setResponse(new JsonResponse(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }
}