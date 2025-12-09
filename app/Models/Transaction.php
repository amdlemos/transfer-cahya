<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/* use Illuminate\Database\Eloquent\Relations\HasMany; */
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Representa uma transaçao monetária entre usuários
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payer_id',
        'payee_id',
        'amount',
        'status',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => TransactionStatus::class,
    ];

    /**
     * Relacionamento com User (pagador)
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    /**
     * Relacionamento com User (recebedor)
     */
    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id');
    }

    /**
     * Relacionamento com TransactionAuthorization
     */
    public function authorization(): HasOne
    {
        return $this->hasOne(TransactionAuthorization::class);
    }

    /**
     * Relacionamento com Notifications
     */
    /* public function notifications(): HasMany */
    /* { */
    /*     return $this->hasMany(Notification::class); */
    /* } */

    /**
     * Verifica se a transação foi completada
     */
    public function isCompleted(): bool
    {
        return $this->status === TransactionStatus::Completed;
    }

    /**
     * Verifica se a transação falhou
     */
    public function isFailed(): bool
    {
        return $this->status === TransactionStatus::Failed;
    }

    /**
     * Verifica se a transação está pendente
     */
    public function isPending(): bool
    {
        return $this->status === TransactionStatus::Pending;
    }

    /**
     * Marca transação como completada
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => TransactionStatus::Completed]);
    }

    /**
     * Marca transação como falha
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => TransactionStatus::Failed]);
    }

    /**
     * Filtra transações com status completado
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::Completed);
    }

    /**
     * Filtra transações com status falha
     */
    public function scopeFailed($query)
    {
        return $query->where('status', TransactionStatus::Failed);
    }

    /**
     * Scope para transações de um usuário específico
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('payer_id', $userId)
            ->orWhere('payee_id', $userId);
    }

    /**
     * Scope para transações enviadas por um usuário
     */
    public function scopeSentBy($query, int $userId)
    {
        return $query->where('payer_id', $userId);
    }

    /**
     * Scope para transações recebidas por um usuário
     */
    public function scopeReceivedBy($query, int $userId)
    {
        return $query->where('payee_id', $userId);
    }

    /**
     * Filtra transações com status pendente
     */
    public function scopePending($query)
    {
        return $query->where('status', TransactionStatus::Pending);
    }
}
