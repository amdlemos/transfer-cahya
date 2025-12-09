<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para armazenar dados de autorização de transações
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
