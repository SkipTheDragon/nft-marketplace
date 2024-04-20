<?php

namespace App\Architecture;

/**
 * List of all contract types for NFTs supported by this marketplace.
 */
enum ENFTTypes: string
{
    case COLLECTION = EContractType::COLLECTION->value;
    case EDITION = EContractType::EDITION->value;
}
