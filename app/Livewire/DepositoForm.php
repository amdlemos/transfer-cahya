<?php

namespace App\Livewire;

use App\Exceptions\UnauthorizedTransferException;
use App\Models\Transaction;
use App\Services\DepositService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Componente Livewire para gerenciar depósitos em carteiras de usuários
 */
class DepositoForm extends Component
{
    use Traits\BalanceManagement;

    public ?float $amount = null;

    public ?Transaction $failedTransaction = null;

    public string $successMessage = '';

    public string $errorMessage = '';


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
     * Processa um novo depósito na carteira do usuário
     */
    public function deposit(DepositService $depositService)
    {
        $this->validate();
        $this->reset(['successMessage', 'errorMessage', 'failedTransaction']);

        try {
            $depositService->deposit(auth()->guard()->user(), (float) $this->amount);

            $this->successMessage =
            'Depósito de R$ '.number_format((float) $this->amount, 2, ',', '.').' realizado com sucesso!';

            $this->reset('amount');
            $this->updateBalance();
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

            $this->successMessage =
            'Depósito de R$ '.number_format((float) $transaction->amount, 2, ',', '.').' realizado com sucesso!';

            $this->reset(['failedTransaction', 'amount']);
            $this->updateBalance();
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
