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

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Email $email, ResetToken $token): void
    {
        $message = (new TemplatedEmail())
            ->to($email->getValue())
            ->subject('Password resetting')
            ->htmlTemplate('mail/user/signup.html.twig')
            ->context(['token' => $token]);
            
        $this->mailer->send($message);
    }
}