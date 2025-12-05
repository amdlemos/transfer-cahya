<?php

namespace App\Exceptions;

use Exception;

/**
 * Exceção lançada quando uma transferência não é autorizada.
 */
class UnauthorizedTransferException extends Exception
{
    protected $message = 'Transferência não autorizada';
}
