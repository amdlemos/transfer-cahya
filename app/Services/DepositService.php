<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\UnauthorizedTransferException;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function __construct(
        private AuthorizationService $authorizationService
    ) {}

    /**
     * Realiza um depósito na carteira do usuário
     *
     * @param  User  $user  Usuário que receberá o depósito
     * @param  float  $amount  Valor a ser depositado
     * @return Transaction Transação criada
     *
     * @throws UnauthorizedTransferException Se não autorizado
     */
    public function deposit(User $user, float $amount): Transaction
    {
        return DB::transaction(function () use ($user, $amount) {
            // Criar transação de depósito
            $transaction = Transaction::create([
                'payer_id' => $user->id, // No depósito, o próprio usuário é o pagador
                'payee_id' => $user->id, // E também o recebedor
                'amount' => $amount,
                'status' => TransactionStatus::Pending,
                'description' => 'Depósito',
            ]);

            try {
                // Consultar serviço autorizador
                $authorized = $this->authorizationService->authorize($transaction);
                $authorized = true;

                if (! $authorized) {
                    $transaction->markAsFailed();
                    throw new UnauthorizedTransferException('Depósito não autorizado. Tente novamente.');
                }

                // Creditar na carteira
                $user->wallet->credit($amount);

                // Marcar como completado
                $transaction->markAsCompleted();

                return $transaction;
            } catch (UnauthorizedTransferException $e) {
                // Mantém a transação como Failed para histórico
                throw $e;
            } catch (\Exception $e) {
                // Reverter em caso de outro erro
                $transaction->markAsReversed();
                throw $e;
            }
        });
    }

    /**
     * Tenta novamente processar um depósito que falhou
     *
     * @param  Transaction  $transaction  Transação que falhou
     * @return Transaction Transação atualizada
     *
     * @throws UnauthorizedTransferException Se não autorizado novamente
     */
    public function retryDeposit(Transaction $transaction): Transaction
    {
        if (! $transaction->isFailed()) {
            throw new InsufficientBalanceException;
        }

        return DB::transaction(function () use ($transaction) {
            // Atualizar status para pendente
            $transaction->update(['status' => TransactionStatus::Pending]);

            try {
                // Consultar serviço autorizador novamente
                $authorized = $this->authorizationService->authorize($transaction);
                $authorized = true;

                if (! $authorized) {
                    $transaction->markAsFailed();
                    throw new UnauthorizedTransferException('Depósito não autorizado. Tente novamente.');
                }

                // Creditar na carteira
                $transaction->payee->wallet->credit($transaction->amount);

                // Marcar como completado
                $transaction->markAsCompleted();

                return $transaction;
            } catch (UnauthorizedTransferException $e) {
                throw $e;
            } catch (\Exception $e) {
                $transaction->markAsReversed();
                throw $e;
            }
        });
    }
}
