<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

class Command
{
    public string $network;

    public string $identity;
}