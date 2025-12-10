<?php

namespace App\Livewire;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Componente para exibir gráficos de transações
 */
class TransactionCharts extends Component
{
    /**
     * Obtém dados para gráfico de tipos de transação
     */
    public function getTransactionTypesData(): array
    {
        $userId = Auth::id();

        $transfers = Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Transfer)
            ->where('status', TransactionStatus::Completed)
            ->sum('amount');

        $deposits = Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Deposit)
            ->where('status', TransactionStatus::Completed)
            ->sum('amount');

        $withdrawals = Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Withdrawal)
            ->where('status', TransactionStatus::Completed)
            ->sum('amount');

        return [
            'labels' => [
                TransactionType::Transfer->label(),
                TransactionType::Deposit->label(),
                TransactionType::Withdrawal->label(),
            ],
            'data' => [
                (float) $transfers,
                (float) $deposits,
                (float) $withdrawals,
            ],
        ];
    }

    /**
     * Obtém dados para gráfico de transações enviadas vs recebidas
     * Considera apenas transferências (não inclui depósitos e saques)
     */
    public function getSentVsReceivedData(): array
    {
        $userId = Auth::id();

        $sentAmount = Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Transfer)
            ->where('status', TransactionStatus::Completed)
            ->sum('amount');

        $receivedAmount = Transaction::where('payee_id', $userId)
            ->ofType(TransactionType::Transfer)
            ->where('status', TransactionStatus::Completed)
            ->sum('amount');

        return [
            'labels' => ['Enviadas', 'Recebidas'],
            'data' => [
                (float) $sentAmount,
                (float) $receivedAmount,
            ],
        ];
    }

    /**
     * Obtém o total de transferências enviadas (exclui depósitos e saques)
     */
    public function getTotalSent(): float
    {
        $userId = Auth::id();

        return (float) Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Transfer)
            ->where('status', TransactionStatus::Completed)
            ->sum('amount');
    }

    /**
     * Obtém o total de transferências recebidas (exclui depósitos e saques)
     */
    public function getTotalReceived(): float
    {
        $userId = Auth::id();

        return (float) Transaction::where('payee_id', $userId)
            ->ofType(TransactionType::Transfer)
            ->where('status', TransactionStatus::Completed)
            ->sum('amount');
    }

    /**
     * Obtém o total depositado
     */
    public function getTotalDeposited(): float
    {
        $userId = Auth::id();

        return (float) Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Deposit)
            ->where('status', TransactionStatus::Completed)
            ->sum('amount');
    }

    /**
     * Obtém o total sacado
     */
    public function getTotalWithdrawn(): float
    {
        $userId = Auth::id();

        return (float) Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Withdrawal)
            ->where('status', TransactionStatus::Completed)
            ->sum('amount');
    }

    /**
     * Obtém contagem de transações por tipo
     */
    public function getTransactionTypesCountData(): array
    {
        $userId = Auth::id();

        $transfersCount = Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Transfer)
            ->where('status', TransactionStatus::Completed)
            ->count();

        $depositsCount = Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Deposit)
            ->where('status', TransactionStatus::Completed)
            ->count();

        $withdrawalsCount = Transaction::where('payer_id', $userId)
            ->ofType(TransactionType::Withdrawal)
            ->where('status', TransactionStatus::Completed)
            ->count();

        return [
            'labels' => [
                TransactionType::Transfer->label(),
                TransactionType::Deposit->label(),
                TransactionType::Withdrawal->label(),
            ],
            'data' => [
                $transfersCount,
                $depositsCount,
                $withdrawalsCount,
            ],
        ];
    }

    /**
     * Renderiza a view do componente
     */
    public function render()
    {
        return view('livewire.transaction-charts');
    }
}
