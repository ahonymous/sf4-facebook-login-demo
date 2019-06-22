<?php

namespace App\Service\Traits;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Trait UserAuthenticatorSaveTrait.
 */
trait UserAuthenticatorSaveTrait
{
    use DoctrineTrait;

    private function saveUserByToken(TokenInterface $token): void
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user->getId()) {
            $this->doctrine->getManager()->persist($user);
        }
        $user->setLastLoginAt(new \DateTime());
        $this->doctrine->getManager()->flush($user);
    }
}
