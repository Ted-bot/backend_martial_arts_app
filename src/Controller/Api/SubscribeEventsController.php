<?php

namespace App\Controller\Api;

use App\Class\Role;
use App\Entity\PostEvent;
use App\Repository\PostEventRepository;
use App\Repository\UserProfileRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubscribeEventsController extends AbstractController
{
    public function __construct(
        private Security $security,
    ){}

    #[Route('/api/subscribe/events/{id}', name: 'api_subscribe_events')]
    public function subscribe(
        PostEvent $event,
        EntityManager $post, 
        UserProfileRepository $userProfileRepository,
        Request $request
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // dd($event->startDate);

        $currentUser = $this->getUser();
        $userProfile = $userProfileRepository->find($currentUser->getId());
        $event->addSubscribe($userProfile);
        $post->persist($event);

        $timeEvent = date_format($event->startDate,'d-M H:m');

        try {
            $post->flush();

            return new Response("You have assigned to {$event->title} \n on {$timeEvent}, Cant wait to se you there !", Response::HTTP_CREATED);
        } catch (UniqueConstraintViolationException $e) {

            dd($e->getMessage());
            // $sqlState = 0;
            // $sqlState = $e->getSQLState();

            // $field = [];
            // $message = 'Please check your input and make sure email and phone are unique to our database';

            // if($e->getSQLState() == 23505){
            //     array_push($field, 'email');
            //     $message = 'Your email address is known to our database, please reset password if you forgot';
            // }

            // return $this->json([
            //     'errors' => [
            //         'error' => $e->getMessage(),
            //         'property' => $field,
            //         'sql_state' => $sqlState,
            //         'message' => $message
            //     ]
            // ], 
            // 400,
            // [
            //     'Content-Type' =>  'application/json'
            // ]);
        }
    }

    #[Route('/api/subscribe/events/{id}/delete', name: 'api_unsubscribe_events')]
    public function unscubscribe(PostEvent $event, EntityManager $post, UserProfileRepository $userProfileRepository): Response
    {
        $currentUser = $this->getUser();
        $userProfile = $userProfileRepository->find($currentUser->getId());
        $event->removeSubscribe($userProfile);
        $post->persist($event);
        $post->flush();
        
        return new Response('deleted subscribed EVent');
    }

    #[Route('/api/subscribe/events/{id}/test', name: 'api_unsubscribe_events')]
    public function test(Request $request, PostEventRepository $post, UserProfileRepository $userProfileRepository): JsonResponse
    {
        
        // dd();
        $getAll =$post->findSubscribtionIdsByEventId($request->get('event_id'));
        // $post->findAllSubscribtions($event->id);
        // $currentUser = $this->getUser();
        // $userProfile = $userProfileRepository->find($currentUser->getId());
        // $event->removeSubscribe($userProfile);
        // $post->persist($event);
        // $post->flush();
        
        return new JsonResponse(['deleted subscribed EVent'=> $getAll], Response::HTTP_OK);
    }
}
