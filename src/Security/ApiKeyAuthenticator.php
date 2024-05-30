<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\SecurityRequestAttributes as Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        // dd('support');
        $pathV1Valid = strpos($request->getPathInfo(), '/api/v1/');
        $pathV2Valid = strpos($request->getPathInfo(), '/api/v2/');
        // dd(['v1 '=> $pathV1Valid, 'v2' => $pathV2Valid]);
        // dd($request->getPathInfo());
        return $pathV1Valid === 0 || $pathV2Valid === 0  && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $data = $request->toArray();
        $email = $data['email'];
        $password = $data['password'];

        return new Passport(
            new UserBadge($email, function($userIdentifier){
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                if(!$user) {
                    throw new UserNotFoundException();
                }

                return $user;
            }), 
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        // return new JsonResponse([],200);
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return null;
        // dd('failure');
        // $data = [
        //     // you may want to customize or obfuscate the message first
        //     'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

        //     // or to translate this message
        //     // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        // ];

        // return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}