<?php

namespace App;

/**
 * Representa o status de uma transação
 *
 * - Completed: transaçãao efetuada com sucesso
 * - Failed: não autorizada
 */
enum TransactionsStatus: string
{
    case Completed = 'completed';
    case Failed = 'failed';
}
