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
        return new UserBadge($token);
    }
}