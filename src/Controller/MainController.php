<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PostEvent;
use App\Entity\UserProfile;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{

    #[Route('/', name : "new_app")]
    public function index(): Response
    // public function index(): Response
    {
        return new Response('BDMA API');
    }

    // #[Route('/testuser', name: 'test_user')]
    // public function add(): Response
    // {
    //     // $user = new User();

    //     // $user->getId(1);

    //     $profile = new PostEvent();
    //     $profile->getId(1);
    //     $profile_id = $profile->getUserUniq();
    //     $email = $user->getEmail();

    //     return $this->render(
    //         'main/index.html.twig',
    //         [
    //             'user' => $email,
    //             'profile_id' => $profile_id
    //         ]
    //     );
    // }

    #[Route('/show/{id}', name: 'test_area')]
    public function show(EntityManager $entityManager, int $id): Response
    {
        $post_event = $entityManager->getRepository(UserProfile::class)->find($id);

        if (!$post_event) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return new Response('Check out your post: '.$post_event->getConversion());
    }

}