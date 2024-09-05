<?php


namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
// use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
class ApiTokenHandler implements AccessTokenHandlerInterface
{

    public function __construct(
        private UserRepository $userRepository, 
        // private JWTAuthenticator $authenticator,
        private User $user
        )
    {}
    public function getUserBadgeFrom(string $token): UserBadge
    {
        $token = $this->userRepository->findOneBy(['email' => $token]);
        // $this->user->setEmail($token);
        // $userIdentifier = $this->user->getUserIdentifier();
        // dd([ 'userIdentifier' => $userIdentifier]);
        // $user = $this->authenticator->loadUser(['token' => $token], 'username');

        // dd($token->getUserIdentifier());
        // return new UserBadge($userIdentifier);
        return new UserBadge($token);
        // return new UserBadge($user);
    }
}