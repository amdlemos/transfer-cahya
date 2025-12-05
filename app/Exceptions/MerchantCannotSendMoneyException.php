<?php

namespace App\Exceptions;

use Exception;

/**
 * Exceção lançada quando um lojista tenta enviar dinheiro,
 * o que não é permitido.
 */
class MerchantCannotSendMoneyException extends Exception
{
    protected $message = 'Lojistas não podem enviar dinheiro';
}
