<?php

namespace App\Livewire;

use App\Exceptions\UnauthorizedTransferException;
use App\Livewire\Traits\BalanceManagement;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Componente Livewire para realizar transferências entre usuários
 */
class TransferForm extends Component
{
    use BalanceManagement;

    public ?int $payeeId = null;

    public ?float $amount = null;

    public string $successMessage = '';

    public string $errorMessage = '';

    protected $rules = [
        'amount' => 'required|numeric|min:0.01|max:999999.99',
        'payeeId' => 'required|integer|exists:users,id',
    ];

    protected $messages = [
        'amount.required' => 'O valor é obrigatório.',
        'amount.numeric' => 'O valor deve ser numérico.',
        'amount.min' => 'O valor mínimo é R$ 0,01.',
        'amount.max' => 'O valor máximo é R$ 999.999,99.',
        'payeeId.required' => 'Destinatário é obrigatório.',
    ];

    /**
     * Realiza uma transferência para outro usuário
     */
    public function transfer(TransferService $transferService)
    {
        \Log::info('TransferForm::transfer() chamado', [
            'payeeId' => $this->payeeId,
            'amount' => $this->amount,
        ]);

        $this->validate();

        $this->reset(['successMessage', 'errorMessage']);

        try {
            $payer = Auth::user();
            $payee = User::find($this->payeeId);

            \Log::info('Iniciando transferência', [
                'payerId' => $payer->id,
                'payeeId' => $payee->id,
                'amount' => $this->amount,
            ]);

            $transaction = $transferService->transfer($payer, $payee, (float) $this->amount);

            \Log::info('Transferência completada', ['transaction_id' => $transaction->id]);

            $this->successMessage = 'Transferência de R$ '.number_format((float) $this->amount, 2, ',', '.').' realizada com sucesso! (ID: '.$transaction->id.')';
            $this->reset(['amount', 'payeeId']);
            $this->updateBalance();
        } catch (UnauthorizedTransferException $e) {
            \Log::warning('Transferência não autorizada', ['error' => $e->getMessage()]);

            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            \Log::error('Erro ao processar transferência', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->errorMessage = 'Erro ao processar transferência: '.$e->getMessage();
        }
    }

    /**
     * Renderiza a view do componente
     */
    public function render()
    {
        $users = User::where('id', '<>', Auth::user()->id)->orderBy('name')->get();

        return view('livewire.transfer-form', [
            'users' => $users,
        ]);
    }
}
