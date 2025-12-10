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
    /**
     * Usuário comum do sistema
     */
    case Common = 'common';

    /**
     * Usuário lojista com restrições de transferência
     */
    case Merchant = 'merchant';

    /**
     * Verifica se o tipo de usuário é um lojista
     */
    public function isMerchant(): bool
    {
        return $this === self::Merchant;
    }

    /**
     * Verifica se o usuário pode realizar transferências
     */
    public function canTransfer(): bool
    {
        return $this === self::Common;
    }

    /**
     * Retorna o rótulo legível do tipo de usuário
     */
    public function labels(): string
    {
        return match ($this) {
            self::Common => 'Comun',
            self::Merchant => 'Logista',
        };
    }
}
