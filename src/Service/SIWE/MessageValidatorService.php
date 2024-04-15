<?php

namespace App\Service\SIWE;

use Elliptic\EC;
use Exception;
use kornrunner\Keccak;

class MessageValidatorService
{
    /**
     * @throws Exception
     */
    public function publicKeyToAddress(string $publicKey): string
    {
        return "0x" . substr(Keccak::hash(substr(hex2bin($publicKey), 1), 256), 24);
    }

    /**
     * @throws Exception
     */
    public function isSignatureValid(string $message, string $signature, string $address): bool
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
}
