<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Representa um usuário da aplicação.
 */
class User extends Authenticatable
{
    /**
     * * Factory para testes e criação de instâncias.
     *
     * @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'full_name',
        'document',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'type' => UserType::class,
        ];
    }

    /**
     * Relacionamento com Wallet
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Transações enviadas
     */
    public function sentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'payer_id');
    }

    /**
     * Transações recebidas
     */
    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'payee_id');
    }

    /**
     * Todas as transações (enviadas e recebidas)
     */
    public function transactions()
    {
        return Transaction::forUser($this->id);
    }

    /**
     * Verifica se pode enviar dinheiro
     */
    public function canSendMoney(): bool
    {
        return $this->type->canSendMoney();
    }
}
