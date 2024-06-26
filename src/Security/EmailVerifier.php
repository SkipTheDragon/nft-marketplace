<?php

namespace App\Security;

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

readonly class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface            $mailer,
        private EntityManagerInterface     $entityManager
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, Account $account, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $account->getId(),
            $account->getEmail(),
            ['id' => $account->getId()]
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    public function handleEmailConfirmation(Request $request, Account $account): void
    {
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, $account->getId(), $account->getEmail());

        $account->setVerified(true);

        $this->entityManager->persist($account);
        $this->entityManager->flush();
    }
}
