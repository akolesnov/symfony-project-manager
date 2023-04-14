<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ResetTokenSender
{
    private $mailer;
    private $from;

    public function __construct(MailerInterface $mailer, string $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function send(Email $email, ResetToken $token): void
    {
        $message = (new TemplatedEmail())
            ->from($this->from)
            ->to($email->getValue())
            ->subject('Password resetting')
            ->htmlTemplate('mail/user/reset.html.twig')
            ->context(['token' => $token]);
            
        if (!$this->mailer->send($message)) {
            throw new \RuntimeException('Unable to send message.');
        }
    }
}