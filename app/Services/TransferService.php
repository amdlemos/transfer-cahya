<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\MerchantCannotSendMoneyException;
use App\Exceptions\UnauthorizedTransferException;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Serviço responsável por gerenciar transferências entre usuários
 */
class TransferService
{
    /**
     * Inicializa o serviço de transferência com as dependências necessárias.
     *
     * @param  AuthorizationService  $authorizationService  Serviço de autorização de transações
     */
    public function __construct(
        private AuthorizationService $authorizationService
    ) {}

    /**
     * Realiza uma transferência entre usuários
     *
     * @param  User  $payer  Usuário que envia o valor
     * @param  User  $payee  Usuário que recebe o valor
     * @param  float  $amount  Valor a ser transferido
     *
     * @throws MerchantCannotSendMoneyException
     * @throws InsufficientBalanceException
     * @throws UnauthorizedTransferException
     */
    public function transfer(User $payer, User $payee, float $amount): Transaction
    {
        if (! $payer->canSendMoney()) {
            throw new MerchantCannotSendMoneyException;
        }

        $payerWallet = $payer->wallet;

        if (! $payerWallet->hasSufficientBalance($amount)) {
            throw new InsufficientBalanceException;
        }

        // 1. Criar log da tentativa (fora da transaction)
        $transaction = Transaction::create([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => $amount,
            'status' => TransactionStatus::Pending,
            'type' => TransactionType::Transfer,
            'description' => 'Transferência entre usuários',
        ]);

        $this->processTransfer($transaction, $payer, $payee, $amount);

        return $transaction;
    }

    /**
     * Tenta novamente uma transferência que falhou
     *
     * @param  Transaction  $transaction  Transação a ser retentada
     * @return Transaction Transação atualizada
     *
     * @throws UnauthorizedTransferException
     */
    public function retryTransfer(Transaction $transaction): Transaction
    {
        if (! $transaction->isFailed()) {
            throw new Exception('Apenas transações com falha podem ser tentadas novamente.');
        }

        $transaction->update(['status' => TransactionStatus::Pending]);

        $this->processTransfer($transaction, $transaction->payer, $transaction->payee, $transaction->amount);

        return $transaction->fresh();
    }

    /**
     * Processa a transferência com tratamento de exceções
     *
     * @param  Transaction  $transaction  Transação a processar
     * @param  User  $payer  Usuário que envia
     * @param  User  $payee  Usuário que recebe
     * @param  float  $amount  Valor a transferir
     *
     * @throws UnauthorizedTransferException
     */
    private function processTransfer(Transaction $transaction, User $payer, User $payee, float $amount): void
    {
        try {
            DB::transaction(function () use ($payer, $payee, $amount, $transaction) {
                $authorized = $this->authorizationService->authorize($transaction);

                if (! $authorized) {
                    throw new UnauthorizedTransferException('Transferência não autorizada.');
                }

                $this->executeWalletTransfer($payer, $payee, $amount, $transaction);

                return $transaction->fresh();
            });
        } catch (UnauthorizedTransferException $e) {
            $transaction->markAsFailed();
            throw $e;
        } catch (Throwable $e) {
            $transaction->markAsReversed();
            throw $e;
        }
    }

    /**
     * Executa o débito/crédito entre carteiras e marca a transação como completada.
     *
     * @param  User  $payer  Usuário que envia
     * @param  User  $payee  Usuário que recebe
     * @param  float  $amount  Valor a transferir
     * @param  Transaction  $transaction  Transação a completar
     */
    private function executeWalletTransfer(User $payer, User $payee, float $amount, Transaction $transaction): void
    {
        $payerWallet = $payer->wallet()->lockForUpdate()->first();
        $payeeWallet = $payee->wallet()->lockForUpdate()->first();

        $payerWallet->debit($amount);
        $payeeWallet->credit($amount);

        $transaction->markAsCompleted();
    }
}
