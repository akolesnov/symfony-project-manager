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
        return self::identityByUser($user);
    }

    public function supportsClass(string $class): bool
    {
        return $class === UserIdentity::class;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . \get_class($user));
        }
        $user = $this->loadUser($user->getUsername());
        return self::identityByUser($user);
    }

    private function loadUser($username): AuthView
    {
        if (!$user = $this->users->findForAuth($username)) {
            throw new UserNotFoundException('');
        }
        return $user;
    }

    private static function identityByUser(AuthView $user): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->email,
            $user->password_hash,
            $user->role,
            $user->status
        );
    }
}