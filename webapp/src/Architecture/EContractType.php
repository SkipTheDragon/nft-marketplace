<?php

namespace App\Architecture;

/**
 * List of all contract types
 */
enum EContractType: string
{
    case MARKETPLACE = 'MARKETPLACE';
    case NFT_COLLECTION = 'ERC721';
    case NFT_EDITION = 'ERC1155';
}
