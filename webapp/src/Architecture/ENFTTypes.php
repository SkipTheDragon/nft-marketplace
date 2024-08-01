<?php

namespace App\Architecture;

/**
 * List of all contract types for NFTs supported by this marketplace.
 */
enum ENFTTypes: string
{
    case COLLECTION = EContractType::NFT_COLLECTION->value;
    case EDITION = EContractType::NFT_EDITION->value;

    public static function fromValue(mixed $contractType): ENFTTypes
    {
        return match ($contractType) {
            EContractType::NFT_COLLECTION->value => self::COLLECTION,
            EContractType::NFT_EDITION->value => self::EDITION,
            default => throw new \InvalidArgumentException('Invalid contract type')
        };
    }
}
