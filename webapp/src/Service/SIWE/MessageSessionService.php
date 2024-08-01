<?php

namespace App\Service\SIWE;

use DateInterval;
use DateTime;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class MessageSessionService
{
    public function __construct(
        #[Autowire("%env%")]
        protected array        $env,
        protected RequestStack $request,
    )
    {
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
    public function deleteTemporaryMessage(): void
    {
        $this->request->getSession()->remove('connect_message');
    }
}
