<?php

namespace Acme\ServerBundle\Security;

use Acme\ServerBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\HttpUtils;

class ApiTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    protected $httpUtils;

    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    public function createToken(Request $request, $providerKey)
    {
        $token = $request->headers->get('token');

        return new PreAuthenticatedToken(
            new User(),
            $token,
            $providerKey
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiTokenUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $token = $token->getCredentials();

        if (!$token) {
            return $this->getPreAuthenticatedToken(new User(), ['IS_AUTHENTICATED_ANONYMOUSLY']);
        }

        $user = $userProvider->loadUserByUsername($token);

        if (is_null($user)) {
            throw new CustomUserMessageAuthenticationException(
                sprintf('Token is incorrect.')
            );
        }

        return $this->getPreAuthenticatedToken($user, ['ROLE_API']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response(
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            Response::HTTP_UNAUTHORIZED
        );
    }

    private function getPreAuthenticatedToken($user, $roles, $token = '', $providerKey = 'secured_area')
    {
        return new PreAuthenticatedToken(
            $user,
            $token,
            $providerKey,
            $roles
        );
    }
}
