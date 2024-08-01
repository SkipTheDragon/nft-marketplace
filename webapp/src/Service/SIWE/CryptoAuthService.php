<?php

namespace App\Service\SIWE;

use App\Entity\Account;
use App\Entity\AccountSession;
use App\Entity\AccountWallet;
use App\TransferObject\ConnectDto;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class CryptoAuthService
{

    public function __construct(
        protected EntityManagerInterface  $entityManager,
        protected RequestStack            $request,
        protected MessageSessionService   $messageSessionService,
        protected MessageValidatorService $messageValidatorService,
        #[Autowire("%env%")]
        protected array                   $env,
    )
    {
    }

    /**
     * @param Account $existingAccount
     * @return void
     */
    public function startNewSession(Account $existingAccount): void
    {
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $browserInfo = $this->parseUserAgentForSession();

        $accountSession = new AccountSession();
        $accountSession->setAccount($existingAccount);
        $accountSession->setToken($token);
        $accountSession->setOs($browserInfo['os']);
        $accountSession->setBrowser($browserInfo['browser']);
        $accountSession->setLastActivityAt(new \DateTimeImmutable());

        $this->entityManager->persist($accountSession);
        $this->entityManager->flush();

        $this->request->getSession()->set('X-AUTH-TOKEN', $token);
    }

    public function parseUserAgentForSession(): array
    {
        $browser = get_browser(null, true);

        return [
            'os' => $browser['platform'] ?? 'Could not detect OS',
            'browser' => $browser['browser'] ?? 'Could not detect browser'
        ];
    }

    /**
     * @throws Exception
     */
    public function handleConnect(ConnectDto $connectDto): ?Account
    {
        $account = null;
        $messageOnServer = $this->messageSessionService->getTemporaryMessage();

        // Check if the user didn't tamper with the message.
        if (
            !str_contains($messageOnServer, $connectDto->address) ||
            !str_contains($messageOnServer, $connectDto->chainId) ||
            (is_string($connectDto->type) && !str_contains($messageOnServer, $connectDto->type)) ||
            !str_contains($messageOnServer, $this->env['app_name']) ||
            !str_contains($messageOnServer, $this->env['app_url'])
        ) {
            throw new Exception('Invalid message.');
        }

        // Check if the message is expired. By checking the expiration time inside the string:
        $currentTime = (new DateTime())->format('Y-m-d H:i:s');
        $expiresAt = explode('Expiration Time: ', $messageOnServer)[1];

        if ($currentTime > $expiresAt) {
            throw new Exception('Expired message.');
        }

        // Delete the message from the session. To prevent replay attacks.
        $this->messageSessionService->deleteTemporaryMessage();

        // Check if the signature is valid.
        if ($this->messageValidatorService->isSignatureValid($messageOnServer, $connectDto->signature, $connectDto->address)) {

            // Check if the wallet is already registered.
            $existingWallet = $this->entityManager->getRepository(AccountWallet::class)->findOneBy(['address' => $connectDto->address]);

            // Register a new wallet + account if it doesn't exist.
            if ($existingWallet === null) {
                $tempAccount = (new Account())->setUsername($connectDto->address);
                $existingWallet = (new AccountWallet())
                    ->setAddress($connectDto->address)
                    ->setType($connectDto->type)
                    ->setAccount($tempAccount);
                $this->entityManager->persist($tempAccount);
                $this->entityManager->persist($existingWallet);

                // Flushing the entity when starting a new session.
            }

            $account = $existingWallet->getAccount();
            // If the account is already registered, we just need to start a new session.
            $this->startNewSession($account);
        } else {
            throw new Exception('Invalid signature.');
        }

        return $account;
    }
}
