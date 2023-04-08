<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
class ResetToken
{
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string $token;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private \DateTimeImmutable $expires;

    public function __construct(string $token, \DateTimeImmutable $expires)
    {
        Assert::notEmpty($token);
        $this->token = $token;
        $this->expires = $expires;
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    public function getToken(): string
    {
        return $this->token;
    }
    
    /**
     * @internal for postLoad callback
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->token);
    }
}