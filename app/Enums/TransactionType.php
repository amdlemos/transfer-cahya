<?php

namespace App\Enums;

/**
 * Representa os tipos de transações do sistema.
 *
 * - Transfer: transferência entre usuários
 * - Deposit: depósito na carteira
 * - Withdrawal: saque da carteira
 */ enum TransactionType: string
{
    case Transfer = 'transfer';
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';

    /**
     * Get a human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::Transfer => 'Transferência',
            self::Deposit => 'Depósito',
            self::Withdrawal => 'Saque',
        };
    }
}
