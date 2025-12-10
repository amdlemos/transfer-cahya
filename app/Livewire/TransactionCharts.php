<?php

namespace App\Livewire;

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
            ->where('description', 'Transferência entre usuários')
            ->sum('amount');

        $deposits = Transaction::where('payer_id', $userId)
            ->where('description', 'Depósito')
            ->sum('amount');

        $withdrawals = Transaction::where('payer_id', $userId)
            ->where('description', 'Saque')
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
     */
    public function getSentVsReceivedData(): array
    {
        $user = Auth::user();

        $sentAmount = $user->sentTransactions()->sum('amount');
        $receivedAmount = $user->receivedTransactions()->sum('amount');

        return [
            'labels' => ['Enviadas', 'Recebidas'],
            'data' => [
                (float) $sentAmount,
                (float) $receivedAmount,
            ],
        ];
    }

    /**
     * Obtém contagem de transações por tipo
     */
    public function getTransactionTypesCountData(): array
    {
        $userId = Auth::id();

        $transfersCount = Transaction::where('payer_id', $userId)
            ->where('description', 'Transferência entre usuários')
            ->count();

        $depositsCount = Transaction::where('payer_id', $userId)
            ->where('description', 'Depósito')
            ->count();

        $withdrawalsCount = Transaction::where('payer_id', $userId)
            ->where('description', 'Saque')
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
