<?php

namespace App\TransferObject;

use App\Architecture\EAccountWallet;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ConnectDto
{
    public function __construct(
        public string         $address,
        public string         $signature,
        #[Assert\NotNull()]
        #[Assert\NotBlank()]
        public EAccountWallet $type,
        public int         $chainId,
    )
    {
    }
}
