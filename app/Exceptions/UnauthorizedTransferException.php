<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedTransferException extends Exception
{
    protected $message = 'Transferência não autorizada';
}
