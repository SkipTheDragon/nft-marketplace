<?php

namespace App\Architecture;

/**
 * List of all contract types
 */
enum EContractType: string
{
    case MARKETPLACE = 'marketplace';
    case COLLECTION = 'collection';
    case EDITION = 'edition';
}
