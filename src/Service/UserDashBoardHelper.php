<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Subscription;
use App\Entity\TokenManager;
use App\Dto\UserDashboard\DashBoardDto;
use App\Repository\PostEventRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Dto\UserDashboard\NextTrainingSessionDto;

class UserDashBoardHelper
{
    private $serializer;

    public function __construct(
        private PostEventRepository $psRepo,
    )
    {
        // $encoders = [new XmlEncoder(), new JsonEncoder()];
        // $normalizers = [new ObjectNormalizer()];
        // $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function userSubscriptionToApiDto(Subscription $subscription, $id, $firstName, $lastName)
    {
        $userTrainingSessions = $this->psRepo
            ->findUserPreviousSubscribedAndPublishedEvents($id);
            
        $userTrainingSession = $this->psRepo
            ->findUserUpcomingSubscribedAndPublishedEvent($id);

            // dd("userTraining",$userTrainingSessions, 'single', $userTrainingSession);

        $userDashBoardData = new DashBoardDto();
        $next_traing_day = new NextTrainingSessionDto();
        $tokenManager = $subscription?->getTokenManager() ?? new TokenManager();
        $userDashBoardData->tokens_owned = $tokenManager?->getTokens() ? $tokenManager?->getTokens() : 0;
        $userDashBoardData->name_subscription = is_null($subscription?->getStatus()) ? 'No valid subscription' : $subscription?->getSubscribedProduct()?->getName();
        $userDashBoardData->start = $subscription?->getDateStart()->format('d-m-Y') ?? 'No valid Subscription';
        $userDashBoardData->end = $subscription?->getDateEnd()->format('d-m-Y') ?? '';
        $userDashBoardData->userFullName = $firstName . ' ' . $lastName;
        $userDashBoardData->sessions_followed = $userTrainingSessions[0][1];

        if($userTrainingSession != null){
            $next_traing_day->next_training_day = $userTrainingSession["startDate"]->format('F jS, Y');
            $next_traing_day->start = $userTrainingSession["startDate"]->format('H:i');
            $next_traing_day->end = $userTrainingSession["endDate"]->format('H:i');
        } else {
            $next_traing_day->next_training_day = 'none selected';
            $next_traing_day->start = 'select a day';
            $next_traing_day->end = ' to train';
        }

        $userDashBoardData->next_session = $next_traing_day;

        return $userDashBoardData;
    }
}