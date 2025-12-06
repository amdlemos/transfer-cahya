<?php

namespace App\Enums;

/**
 * Representa os tipos de usuários do sistema.
 *
 * - Common: usuário comum
 * - Merchant: usuário lojista
 */
enum UserType: string
{
    case Common = 'common';
    case Merchant = 'merchant';

    public function isMerchant(): bool
    {
        return $this === self::Merchant;
    }
}
