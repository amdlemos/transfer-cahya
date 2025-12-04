<?php

namespace App\Support;

class DocumentGenerator
{
    // CPF: 11 dígitos
    public static function cpf(): string
    {
        $n = [];

        for ($i = 0; $i < 9; $i++) {
            $n[$i] = rand(0, 9);
        }

        // dígito 1
        $d1 = 0;
        for ($i = 0, $x = 10; $i < 9; $i++, $x--) {
            $d1 += $n[$i] * $x;
        }
        $d1 = ($d1 % 11 < 2) ? 0 : 11 - ($d1 % 11);

        // dígito 2
        $d2 = 0;
        for ($i = 0, $x = 11; $i < 9; $i++, $x--) {
            $d2 += $n[$i] * $x;
        }
        $d2 += $d1 * 2;
        $d2 = ($d2 % 11 < 2) ? 0 : 11 - ($d2 % 11);

        return implode('', $n) . $d1 . $d2;
    }

    // CNPJ: 14 dígitos
    public static function cnpj(): string
    {
        $n = [];

        for ($i = 0; $i < 12; $i++) {
            $n[$i] = rand(0, 9);
        }

        // dígito 1
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $n[$i] * $weights1[$i];
        }
        $d1 = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        // dígito 2
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $n[$i] * $weights2[$i];
        }
        $sum += $d1 * 2;
        $d2 = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        return implode('', $n) . $d1 . $d2;
    }

    // Decide com base no tipo
    public static function forType(string $type): string
    {
        return $type === 'merchant'
            ? static::cnpj()
            : static::cpf();
    }
}
