<?php

namespace App\Exceptions;

use Exception;

/**
 * Exceção lançada quando o usuário não possui saldo suficiente.
 */
class InsufficientBalanceException extends Exception
{
    protected $message = 'Saldo insuficiente para realizar a transferência';
}
