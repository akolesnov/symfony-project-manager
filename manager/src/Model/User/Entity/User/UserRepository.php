<?php

namespace App\Model\User\Entity\User;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    private $em;

    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repo->findOneBy(['confirmToken' => $token]);
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function findByResetToken(string $token): ?User
    {
        return $this->repo->findOneBy(['resetToken.token' => $token]);
    }

    public function get(Id $id): User
    {
        if (!$user = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }

    public function getByEmail(Email $email): User
    {
        if (!$user = $this->repo->findOneBy(['email' => $email])) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }
    
    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->innerJoin('t.networks', 'n')
                ->andWhere('n.network = :network and n.identity = :identity')
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()->getScalarResult() > 0;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}