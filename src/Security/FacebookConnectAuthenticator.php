<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\FacebookException;
use App\Service\Traits\FacebookManagerTrait;
use App\Service\Traits\LoggerTrait;
use App\Service\Traits\RouterTrait;
use App\Service\Traits\UserAuthenticatorSaveTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class FacebookConnectAuthenticator.
 */
final class FacebookConnectAuthenticator extends AbstractGuardAuthenticator
{
    use RouterTrait;
    use LoggerTrait;
    use FacebookManagerTrait;
    use UserAuthenticatorSaveTrait;

    /**
     * @see AuthenticationEntryPointInterface
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            'message' => 'Authentication Required',
        ];

        return new RedirectResponse($this->router->generate('index'));
    }

    /**
     * @see AuthenticatorInterface
     */
    public function getCredentials(Request $request): array
    {
        try {
            $user = $this->facebookManager->getFbUserData($request);

            return [
                'email' => $user->getEmail(),
                'facebookId' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'fullName' => $user->getName(),
            ];
        } catch (FacebookException $e) {
            throw new \UnexpectedValueException();
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $user = $userProvider->loadUserByUsername($credentials['email']);
        } catch (UsernameNotFoundException $e) {
            $this->logger->info($e->getMessage());

            $user = User::createFacebookUser(
                $credentials['email'],
                $credentials['facebookId'],
                $credentials['firstName'],
                $credentials['lastName'],
                $credentials['fullName']
            );
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new AuthenticationException();
        }

        if (!$user->getFacebookId()) {
            $user->setFacebookId($credentials['facebookId']);
        }

        if (!$user->getFirstName()) {
            $user->setFirstName($credentials['firstName']);
        }

        if (!$user->getLastName()) {
            $user->setLastName($credentials['lastName']);
        }

        return $user->getEmail() === $credentials['email'];
    }

    /**
     * @see AuthenticatorInterface
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->getSession() instanceof SessionInterface) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('index'));
    }

    /**
     * @see AuthenticatorInterface
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->saveUserByToken($token);
    }

    /**
     * @see AuthenticatorInterface
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }

    /**
     * @see AuthenticatorInterface
     */
    public function supports(Request $request): bool
    {
        return 'facebook' === $request->attributes->get('_route');
    }
}
