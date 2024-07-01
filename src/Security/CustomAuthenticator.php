<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;

class CustomAuthenticator extends JWTAuthenticator
{
    protected function loadUser(array $payload, string $identity): UserInterface
    {
        /** @var UserInterface|User $user */
        $user  = parent::loadUser($payload, $identity);
        if (true){
            $ex = new UserNotFoundException('Your account has been deactivated by the administrators');
            $ex->setUserIdentifier($identity);
            throw $ex;
        }
        return $user;
    }
}