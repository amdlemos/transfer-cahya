<?php

namespace App\Models;

use App\Exceptions\InsufficientBalanceException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Representa a carteira/conta de um usuário
 */
class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se tem saldo suficiente
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Debita valor da carteira
     */
    public function debit(float $amount): void
    {
        if (! $this->hasSufficientBalance($amount)) {
            throw new InsufficientBalanceException;
        }

        $this->decrement('balance', $amount);
    }

    /**
     * Credita valor na carteira
     */
    public function credit(float $amount): void
    {
        $this->increment('balance', $amount);
    }
    //
}
