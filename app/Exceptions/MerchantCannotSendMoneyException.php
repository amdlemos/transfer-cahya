<?php

namespace App\Exceptions;

use Exception;

class MerchantCannotSendMoneyException extends Exception
{
    protected $message = 'Lojistas não podem enviar dinheiro';
}
