<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Model\User\UseCase\Reset;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    private $loggger;

    public function __construct(LoggerInterface $logger)
    {
        $this->loggger = $logger;
    }

    #[Route('/reset', name: 'auth.reset')]
    public function request(Request $reqest, Reset\Request\Handler $handler): Response
    {
        $command = new Reset\Request\Command();

        $form = $this->createForm(Reset\Request\Form::class, $command);
        $form->handleRequest($reqest);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
            $this->addFlash('success', 'Chek your email.');
            return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->loggger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }

        }
    }
}