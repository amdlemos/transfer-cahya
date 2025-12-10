<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Exceptions\UnauthorizedTransferException;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Serviço responsável por gerenciar operações de depósito em carteiras de usuários
 */
class DepositService
{
    /**
     * Inicializa o serviço de depósito com dependências
     *
     * @param  AuthorizationService  $authorizationService  Serviço de autorização de transações
     */
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
        // 1. Sempre criar log da tentativa (fora da transaction)
        $transaction = Transaction::create([
            'payer_id' => $user->id,
            'payee_id' => $user->id,
            'amount' => $amount,
            'status' => TransactionStatus::Pending,
            'description' => 'Depósito',
        ]);

        try {
            // 2. Parte crítica (movimentação real do dinheiro)
            DB::transaction(function () use ($user, $amount, $transaction) {
                // Consultar serviço autorizador
                $authorized = $this->authorizationService->authorize($transaction);

                if (! $authorized) {
                    throw new UnauthorizedTransferException('Depósito não autorizado. Tente novamente.');
                }

                // Usar lock para evitar race condition
                $wallet = $user->wallet;
                $wallet->credit($amount);

                // Marcar como completado
                $transaction->markAsCompleted();
            });
        } catch (UnauthorizedTransferException $e) {
            // 3. Falha "de negócio" = manter histórico como Failed
            $transaction->markAsFailed();
            throw $e;
        } catch (\Throwable $e) {
            // 4. Falha técnica inesperada = marcar como Reversed
            $transaction->markAsReversed();
            throw $e;
        }

        return $transaction->fresh();
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
            throw new Exception('Apenas transações com falha podem ser tentadas novamente.');
        }

        try {
            // Atualizar status para pendente antes de tentar
            $transaction->update(['status' => TransactionStatus::Pending]);

            // Parte crítica (movimentação real do dinheiro)
            DB::transaction(function () use ($transaction) {
                // Consultar serviço autorizador novamente
                $authorized = $this->authorizationService->authorize($transaction);

                if (! $authorized) {
                    throw new UnauthorizedTransferException;
                }

                // Usar lock para evitar race condition
                $wallet = $transaction->payee->wallet;
                $wallet->credit($transaction->amount);

                // Marcar como completado
                $transaction->markAsCompleted();
            });
        } catch (UnauthorizedTransferException $e) {
            // Falha de autorização = manter como Failed
            $transaction->markAsFailed();
            throw $e;
        } catch (\Throwable $e) {
            // Falha técnica = marcar como Reversed
            $transaction->markAsReversed();
            throw $e;
        }

        return $transaction->fresh();
    }
}
