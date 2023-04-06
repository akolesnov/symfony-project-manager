<?php

namespace App\Model\User\Entity\User;

interface UserRepository
{
    public function findByConfirmToken(string $token);

    public function getByEmail(Email $email): User;
    
    public function hasByEmail(Email $email): bool;

    public function hasByNetworkIdentity(string $network, string $identity): bool;

    public function add(User $user): void;
}