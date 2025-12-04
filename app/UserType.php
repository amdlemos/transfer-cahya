<?php

namespace App;

enum UserType: string
{
    case Common = 'common';
    case Merchant = 'merchant';

    public function isMerchant(): bool
    {
        return $this === self::Merchant;
    }
}
