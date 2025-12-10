<?php

namespace App\Livewire\Traits;

/**
 * Trait para gerenciar saldo de carteira em componentes Livewire
 */
trait BalanceManagement
{
    public float $currentBalance = 0.0;

    /**
     * Inicializa o componente e carrega o saldo atual do usuário
     */
    public function mount()
    {
        $this->updateBalance();
    }

    /**
     * Atualiza o saldo da carteira do usuário autenticado
     */
    public function updateBalance()
    {
        $this->currentBalance = auth()->guard()->user()->wallet->fresh()->balance;
    }
}
