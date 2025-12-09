<?php

namespace App\Livewire;

use App\Exceptions\UnauthorizedTransferException;
use App\Services\DepositService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Componente Livewire para gerenciar depósitos em carteiras de usuários
 */
class DepositoForm extends Component
{
    public $amount = '';

    public $failedTransaction = null;

    public $successMessage = '';

    public $errorMessage = '';

    public $currentBalance;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01|max:999999.99',
    ];

    protected $messages = [
        'amount.required' => 'O valor é obrigatório.',
        'amount.numeric' => 'O valor deve ser numérico.',
        'amount.min' => 'O valor mínimo é R$ 0,01.',
        'amount.max' => 'O valor máximo é R$ 999.999,99.',
    ];

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

    /**
     * Processa um novo depósito na carteira do usuário
     */
    public function deposit(DepositService $depositService)
    {
        $this->validate();
        $this->reset(['successMessage', 'errorMessage', 'failedTransaction']);

        try {
            $depositService->deposit(auth()->guard()->user(), (float) $this->amount);

            $this->successMessage = 'Depósito de R$ '.number_format($this->amount, 2, ',', '.').' realizado com sucesso!';
            $this->reset('amount');

            $this->dispatch('balance-updated');
        } catch (UnauthorizedTransferException $e) {
            $this->failedTransaction = Auth::user()
                ->sentTransactions()
                ->where('status', 'failed')
                ->latest()
                ->first();

            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = 'Erro ao processar depósito: '.$e->getMessage();
        }
    }

    /**
     * Retenta um depósito que falhou anteriormente
     */
    public function retry(DepositService $depositService)
    {
        if (! $this->failedTransaction) {
            return;
        }

        $this->reset(['successMessage', 'errorMessage']);

        try {
            $transaction = $depositService->retryDeposit($this->failedTransaction);

            $this->successMessage = 'Depósito de R$ '.number_format($transaction->amount, 2, ',', '.').' realizado com sucesso!';
            $this->reset(['failedTransaction', 'amount']);

            $this->dispatch('balance-updated');
        } catch (UnauthorizedTransferException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = 'Erro ao processar depósito: '.$e->getMessage();
        }
    }

    /**
     * Renderiza a view do componente
     */
    public function render()
    {
        return view('livewire.deposito-form');
    }
}
