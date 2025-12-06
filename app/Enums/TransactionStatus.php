<?php

namespace App\Enums;

/**
 * Representa o status de uma transação
 *
 * - Completed: transaçãao efetuada com sucesso
 * - Failed: não autorizada
 * - Pending: criada e ainda não  processada
 */
enum TransactionStatus: string
{
    case Completed = 'completed';
    case Failed = 'failed';
    case Pending = 'pending';

    /**
     * Get a human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::Completed => 'Sucesso',
            self::Failed => 'Falhou',
            self::Pending => 'Pendente',
        };
    }
}
