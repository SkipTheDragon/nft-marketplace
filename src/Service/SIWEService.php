<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\AccountSession;
use App\Entity\AccountWallet;
use App\TransferObject\ConnectDto;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Elliptic\EC;
use Exception;
use kornrunner\Keccak;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class SIWEService
{

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected RequestStack           $request,
        #[Autowire("%env%")]
        protected array                  $env
    )
    {
    }

    /**
     * @throws Exception
     */
    protected function publicKeyToAddress(string $publicKey): string
    {
        return "0x" . substr(Keccak::hash(substr(hex2bin($publicKey), 1), 256), 24);
    }

    /**
     * @throws Exception
     */
    protected function isSignatureValid(string $message, string $signature, string $address): bool
    {
        $msgLength = strlen($message);
        $hash = Keccak::hash("\x19Ethereum Signed Message:\n$msgLength{$message}", 256);
        $sign = ["r" => substr($signature, 2, 64),
            "s" => substr($signature, 66, 64)];
        $recId = ord(hex2bin(substr($signature, 130, 2))) - 27;
        if ($recId != ($recId & 1))
            return false;

        $ec = new EC('secp256k1');
        $publicKey = $ec->recoverPubKey($hash, $sign, $recId);

        return $address == $this->publicKeyToAddress($publicKey->encode("hex"));
    }

    /**
     * @param string $address
     * @param int $chainId
     * @return string
     */
    protected function buildMessage(string $address, int $chainId): string
    {
        $issuedAt = (new DateTime())->format('Y-m-d H:i:s');
        $expiresAt = (new DateTime())->add(DateInterval::createFromDateString('30 minutes'))->format('Y-m-d H:i:s');
        $appName = $this->env['app_name'];
        $appUrl = $this->env['app_url'];
        $nonce = bin2hex(openssl_random_pseudo_bytes(16));

        return "
            $appName wants you to sign in with your address:
            $address

            By signing in, you agree to the terms and conditions of $appName.

            URI: $appUrl
            Version: V1.0
            Chain ID: $chainId
            Nonce: $nonce
            Issued At: $issuedAt
            Expiration Time: $expiresAt
        ";
    }

    /**
     * @param string $address
     * @param int $chainId
     * @return void
     */
    public function storeTemporaryMessage(string $address, int $chainId): void
    {
        $this->request->getSession()->set('connect_message', $this->buildMessage($address, $chainId));
    }

    /**
     * @return string
     */
    public function getTemporaryMessage(): string
    {
        return $this->request->getSession()->get('connect_message');
    }

    /**
     * @return void
     */
    protected function deleteTemporaryMessage(): void
    {
        $this->request->getSession()->remove('connect_message');
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

    public function parseUserAgentForSession() : array
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
        $messageOnServer = $this->getTemporaryMessage();

        // Check if the user didn't tamper with the message.
        if (
            !str_contains($messageOnServer, $connectDto->address) ||
            !str_contains($messageOnServer, $connectDto->chainId) ||
            (is_string($connectDto->type) && !str_contains($messageOnServer, $connectDto->type)) ||
            !str_contains($messageOnServer, $this->env['app_name']) ||
            !str_contains($messageOnServer, $this->env['app_url'])
        )
        {
            throw new Exception('Invalid message.');
        }

        // Check if the message is expired. By checking the expiration time inside the string:
        $currentTime = (new DateTime())->format('Y-m-d H:i:s');
        $expiresAt = explode('Expiration Time: ', $messageOnServer)[1];

        if ($currentTime > $expiresAt) {
            throw new Exception('Expired message.');
        }

        // Delete the message from the session. To prevent replay attacks.
        $this->deleteTemporaryMessage();

        // Check if the signature is valid.
        if ($this->isSignatureValid($messageOnServer, $connectDto->signature, $connectDto->address)) {

            // Check if the wallet is already registered.
            $existingWallet = $this->entityManager->getRepository(AccountWallet::class)->findOneBy(['address' => $connectDto->address]);
            // Register a new wallet + account if it doesn't exist.
            if ($existingWallet === null) {
                $account = (new Account())->setUsername($connectDto->address);
                $existingWallet = (new AccountWallet())
                    ->setAddress($connectDto->address)
                    ->setType($connectDto->type)
                    ->setAccount($account);
                $this->entityManager->persist($account);
                $this->entityManager->persist($existingWallet);

                // Flushing the entity when starting a new session.
            }


            // If the account is already registered, we just need to start a new session.
            $this->startNewSession($account);
        } else {
            throw new Exception('Invalid signature.');
        }

        return $account;
    }
}
