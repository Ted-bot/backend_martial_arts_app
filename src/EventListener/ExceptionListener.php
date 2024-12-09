<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{

    public function __construct(private SerializerInterface $serializer)
    {}

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $isHttpEvent = $exception instanceof HttpExceptionInterface || $exception instanceof ResourceNotFoundException;
        $isValidationEvent = $exception->getPrevious() instanceof ValidationFailedException || $exception->getPrevious() instanceof ResourceNotFoundException;

        // We are only interested in validation errors in an httpException context
        if (!$isHttpEvent || !$isValidationEvent) {
            return;
        }

        // if($exception instanceof HttpExceptionInterface){
        //     //Determine status
        //     $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR; 

        //     //Prepare Hydra error response
        //     $error = [
        //         '@content' => '/contexts/Error',
        //         '@type' => 'hydra:error',
        //         'hydra:title' => 'An error occurres',
        //         'hydra:description' => $exception->getMessage(),
        //         'statusCode' => $statusCode
        //     ];

        //     //serialize the response
        //     $json = $this->serializer->serialize($error, 'jsonld');

        //     $response = new JsonResponse($json, $statusCode, [], true);

        //     // Set the response in the Event
        //     $event->setResponse($response);
        // }

        
        if($event->getThrowable()->getPrevious() instanceof ResourceNotFoundException){
            $event->setResponse(new JsonResponse(['message' => $exception->getPrevious()->getMessage()], JsonResponse::HTTP_NOT_FOUND));
        }
        
        if($exception instanceof HttpExceptionInterface)
        {
            $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR; 

            /**
             * @var ValidationFailedException $validationException
             */
            $validationException = $event->getThrowable()->getPrevious();
            $errorMessages = [];
            foreach ($validationException->getViolations() as $violation) {
                $errorMessages[$violation->getPropertyPath()] = $violation->getMessage();
            }
            
            //Prepare Hydra error response
            $error = [
                '@content' => '/contexts/Error',
                '@type' => 'hydra:error',
                'hydra:title' => 'An error occurres',
                'errors' => $errorMessages,
                'statusCode' => $statusCode
            ];

            $json = $this->serializer->serialize($error, 'jsonld');

            $response = new JsonResponse($json, $statusCode, [], true);
    
            $event->setResponse($response, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        // dd(["errorMessages" => $errorMessages, "event" => $event]);        
    }
}