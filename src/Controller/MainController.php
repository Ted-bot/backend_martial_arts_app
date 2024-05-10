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
    public function index(EntityManager $manager): Response
    // public function index(): Response
    {
        $user = new User();
        $user->setEmail('Acce@email.com');
        $user->setFirstName('John');
        $user->setLastName('van den heuvel');
        $user->setConversion('Ik wilde graag trainen :p');
        $user->setGender('man');
        $user->setLocation('Amsterdam');
        $user->setPhoneNumber(123456789);
        $user->setPassword('12345678');

        $postEvent = new UserProfile();
        $postEvent->setName("Name: I see Data");
        $postEvent->setConversion("Yeaaaaaaah !");
        $postEvent->setUserUniq($user);

        $email = $user->getEmail();
        $prf_id = $postEvent->getConversion();
        
        $manager->persist($postEvent);
        $manager->persist($user);
        $manager->flush();

        // return new Response($profiles, 200);
        return $this->render(
            'main/index.html.twig',
            [
                // 'user' => $email,
                'user' => $email,
                'profile_id' => $prf_id
            ]
        );
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
    
    // #[Route('/')]
    // public function add(): Response
    // {


    //     return Response();
    // }

}