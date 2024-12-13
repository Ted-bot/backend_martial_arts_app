<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Mollie\Api\Exceptions\ApiException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{

    public function __construct(private SerializerInterface $serializer)
    {}

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        // dd([ 'eventPrevious' => $exception->getPrevious(),'exception' => $exception]);

        $isHttpEvent = $exception instanceof HttpExceptionInterface || $exception instanceof ResourceNotFoundException || $exception instanceof ApiException;
        $isValidationEvent = $exception->getPrevious() instanceof ValidationFailedException || $exception->getPrevious() instanceof ResourceNotFoundException || $exception instanceof ApiException;

        // dd(['error' => $exception->getPrevious()]);
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

        // dd(['errors' => $exception]);
        if($event->getThrowable()->getPrevious() instanceof ResourceNotFoundException){
            $event->setResponse(new JsonResponse(['message' => $exception->getPrevious()->getMessage()], JsonResponse::HTTP_NOT_FOUND));
        }
        
        if($exception instanceof HttpExceptionInterface || $exception instanceof ApiException)
        {
            // dd(['errors' => $exception->getResponse()->getStatusCode()]);
            // $statusCode = $exception instanceof HttpExceptionInterface || $exception instanceof ApiException ? $exception?->getStatusCode() ?? $exception?->getResponse()->getStatusCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR; 
            $statusCode  = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            if($exception instanceof HttpExceptionInterface){
                $statusCode =  $exception?->getStatusCode();
            } else if ($exception instanceof ApiException){
                $statusCode = $exception?->getResponse()->getStatusCode();
            }
            
            // dd(['errors' => $statusCode]);

            /**
             * @var ValidationFailedException $validationException
             */
            $validationException = $event->getThrowable()?->getPrevious() ?? $event->getThrowable();
            // dd(['validationException' => $event->getThrowable()->getPrevious()]);
            $errorMessages = [];
            if(method_exists($validationException, 'getViolations')){
                foreach ($validationException->getViolations() as $violation) {
                    $errorMessages[$violation->getPropertyPath()] = $violation->getMessage();
                }
            } else {
                // handling mollie errors
                // strip mollie exception error message
                if(str_contains($validationException->getMessage(), 'mollie')){
                    
                    $sendErrorMessage = "";
                    // example error str: [2024-12-12T22:31:08+0000] Error executing API call (422: Unprocessable Entity): The billing phone number is invalid. Field: billingAddress.billingAddress.phone. Documentation: https://docs.mollie.com/reference/v2/payments-api/create-payment
                    preg_match('/\)\: (.*?) Field\:/', $validationException->getMessage(), $matches);
                    $rawMollieMessage = trim($matches[1]);
                    $sendErrorMessage = $rawMollieMessage;
                    //    "matches" => array:2 [ -> between "):" {string} "Field:"
                    //     0 => "): The billing phone number is invalid. Field:"
                    //     1 => "The billing phone number is invalid."
                    //   ]

                    preg_match('/Field\: (.*?) Doc/', $validationException->getMessage(), $fields);
                    // dd([ 'preg_matching_field' => $fields]);
                    $fieldArray = explode(".", $fields[1]);
                    // dd([ 'preg_matching_field' => $fieldArray, 'otherMatches' => $fields]);

                    $errorProperty = $fieldArray[2];

                    // already set within correct error field on frontend, see paymentPage [method] => payOrderHandler() , maybe should do it on backend
                    // get property names https://docs.mollie.com/reference/create-payment
                    // $errorField = "";
                    switch ($errorProperty) {
                        case 'phone':
                            // $errorField =  "phoneNumber";
                            $sendErrorMessage = $rawMollieMessage . " correct e.g: (+) 31 (0) 6 5555 5555";
                            break;
                    }
                    //     case 'givenName' || 'familyName':
                    //         $errorField = "firstAndLastName";
                    //         break;
                    //     case 'streetAndNumber':
                    //         $errorField =  "addressLine";
                    //         break;
                    //     case 'streetAdditional':
                    //         $errorField =  "unitNumber";
                    //         break;
                    //     case 'postalCode':
                    //         $errorField =  "postalCode";
                    //         break;
                    //     case 'email':
                    //         $errorField =  "email";
                    //         break;
                    //     case 'region':
                    //         $errorField =  "state";
                    //         break;
                    //     case 'city':
                    //         $errorField =  "city";
                    //         break;
                    //     default:
                    //         $errorField =  "unkown";
                    // }
                    
                    $errorMessages[$errorProperty] = $sendErrorMessage;
                } else {
                    // note: create logger send data somewhere important
                    $errorMessages['unknown_payment_error'] = $validationException->getMessage();
                }
            }


            // note: possible to create a logger or send error message someWhere ?? use eventHandker ?

            // dd(['errors' => $errorMessages]);
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
    
            $event->setResponse($response); //, JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        }
        // dd(["errorMessages" => $errorMessages, "event" => $event]);        
    }
}