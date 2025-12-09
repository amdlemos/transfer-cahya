<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para armazenar dados de autorização de transações
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $transaction_id
 * @property bool $authorized
 * @property string|null $response
 * @property \Illuminate\Support\Carbon $attempted_at
 * @property-read \App\Models\Transaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization whereAttemptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization whereAuthorized($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAuthorization whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TransactionAuthorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'authorized',
        'response',
        'attempted_at',
    ];

    protected $casts = [
        'authorized' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    /**
     * Relacionamento com Transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
