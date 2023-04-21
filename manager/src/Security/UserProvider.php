<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    public function loadUserByIdentifier(string $username): UserInterface
    {
        $user = $this->loadUser($username);
        return self::identityByUser($user, $username);
    }

    public function supportsClass(string $class): bool
    {
        return $class === UserIdentity::class;
    }

    public function refreshUser(UserInterface $identity)
    {
        if (!$identity instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . \get_class($identity));
        }
        $user = $this->loadUser($identity->getUsername());
        return self::identityByUser($user, $identity->getUsername());
    }

    private function loadUser($username): AuthView
    {
        $chunks = explode(':', $username);

        if (\count($chunks) == 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if ($user = $this->users->findForAuthByEmail($username)) {
            return $user;
        }

        throw new UserNotFoundException('');
    }

    private static function identityByUser(AuthView $user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $username,
            $user->password_hash ?: '',
            $user->role,
            $user->status
        );
    }
}