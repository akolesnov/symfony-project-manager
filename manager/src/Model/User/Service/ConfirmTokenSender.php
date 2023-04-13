<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ConfirmTokenSender
{
    private $mailer;
    private $from;

    public function __construct(MailerInterface $mailer, string $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function send(Email $email, string $token): void
    {
        $message = (new TemplatedEmail())
            ->from($this->from)
            ->to($email->getValue())
            ->subject('Sign Up Confirmation.')
            ->htmlTemplate('mail/user/signup.html.twig')
            ->context(['token' => $token]);
            
        if (!$this->mailer->send($message)) {
            throw new \RuntimeException('Unable to send message.');
        }
    }
}