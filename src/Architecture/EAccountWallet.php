<?php

namespace App\Architecture;

/**
 * Supported account wallets
 */
enum EAccountWallet: string
{
    case METAMASK = 'io.metamask';
    case STUB_FROM_IMPORT = 'stub.from_import';
}
