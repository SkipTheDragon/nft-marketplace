<?php

namespace App\Service\NFTImporter;

use App\Entity\Blockchain;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Handles the RPC providers for a blockchain.
 */
readonly class RpcProviderService
{
    public function __construct(
        protected HttpClientInterface $client,
    )
    {
    }

    /**
     * @param Blockchain $blockchain
     * @return array{provider: string, latency: float}
     */
    public function getProviders(Blockchain $blockchain): array
    {
        $providerUrls = array_map(function ($provider) {
            return $provider->getUrl();
        }, $blockchain->getRpcProviders()->toArray());

        return $this->orderByFastestProvider($providerUrls);
    }

    /**
     * @param Blockchain $blockchain
     * @return array{provider: string, latency: float}
     */
    public function getFastestProvider(Blockchain $blockchain): array
    {
        $providers = $this->getProviders($blockchain);
        return $providers[0];
    }

    /**
     * @param array $providers
     * @return array{provider: string, latency: float}
     */
    public function orderByFastestProvider(array $providers): array
    {
        $providersWithLatency = array_map(function ($provider) {
            return $this->getProviderLatency($provider);
        }, $providers);

        usort($providersWithLatency, function($a, $b) {
            $d1 = $a['latency'];
            $d2 = $b['latency'];

            if ($d1 == $d2) {
                return 0;
            }
            return ($d1 < $d2) ? -1 : 1;
        });

        return $providersWithLatency;
    }

    /**
     * @param string $provider
     * @return array{provider: string, latency: float}
     */
    private function getProviderLatency(string $provider): array
    {
        try {
            $response = $this->client->request(
                'GET',
                $provider
            );

            return [
                'provider' => $provider,
                'latency' => $response->getInfo('total_time'),
            ];
        } catch (TransportExceptionInterface $e) {
            return [
                'provider' => $provider,
                'latency' => -1,
            ];
        }
    }
}
