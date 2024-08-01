<?php

namespace App\Architecture;

/**
 * Blockchain types
 */
enum EBlockchainType: string
{
    case MAIN = 'mainnet';
    case TEST = 'testnet';
}
