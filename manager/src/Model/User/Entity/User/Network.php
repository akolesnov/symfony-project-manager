<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity()]
#[ORM\Table(name: 'user_user_networks')]
#[ORM\UniqueConstraint(columns: ['network', 'identity'])]
class Network
{
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'networks')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
    private string $network;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
    private string $identity;

    public function __construct(User $user, string $network, string $identity)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;
    }

    public function isForNetwork(string $network): bool
    {
        return $this->network === $network;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}