<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\SecurityRequestAttributes as Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository, 
        private JWTEncoderInterface $jwtEncoder,
        // private EntityManager $em
    )
    {
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        $pathV1Valid = strpos($request->getPathInfo(), '/api/v1/');
        $pathV2Valid = strpos($request->getPathInfo(), '/api/v2/');
        return $pathV1Valid === 0 || $pathV2Valid === 0  && $request->isMethod('POST');
    }
    
    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );

        $token = $extractor->extract($request);

        if(!$token) {
            throw new BadCredentialsException();            
        }
        // dd(['token' => $token]);

        return $token;
    }

    public function authenticate(Request $request): Passport
    {
        $data = $request->toArray();
        $email = $data['username'];
        $password = $data['password'];
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $user->getUserIdentifier();

        return new Passport(
            new UserBadge($user->email, function($userIdentifier){
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);
                // var_dump($user);

                if($user === null) {
                    throw new UserNotFoundException('incorrect credentials');
                }

                return $user;
            }), 
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        // $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new JsonResponse(['errors' => 'Login credentials are incorrect!'], 401);
    }
}