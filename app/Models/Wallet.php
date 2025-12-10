<?php

namespace App\Models;

use App\Exceptions\InsufficientBalanceException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Representa a carteira/conta de um usuário
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @property numeric $balance
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereUserId($value)
 *
 * @mixin \Eloquent
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
}
