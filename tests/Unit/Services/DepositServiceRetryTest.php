<?php

namespace Tests\Unit\Services;

use App\Enums\TransactionStatus;
use App\Exceptions\UnauthorizedTransferException;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuthorizationService;
use App\Services\DepositService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class DepositServiceRetryTest extends TestCase
{
    use RefreshDatabase;

    public function test_retry_deposit_success_credits_wallet_and_completes_transaction()
    {
        $user = User::factory()->create();

        // create a failed transaction for this user
        $transaction = Transaction::factory()->create([
            'payer_id' => $user->id,
            'payee_id' => $user->id,
            'amount' => 50,
            'status' => TransactionStatus::Failed->value,
            'description' => 'Depósito',
        ]);

        $authMock = Mockery::mock(AuthorizationService::class);
        $authMock->shouldReceive('authorize')->once()->andReturn(true);

        $this->app->instance(AuthorizationService::class, $authMock);

        $service = $this->app->make(DepositService::class);

        $service->retryDeposit($transaction);

        $this->assertEquals(50.0, (float) $user->wallet->fresh()->balance);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => TransactionStatus::Completed->value,
        ]);
    }

    public function test_retry_deposit_unauthorized_keeps_failed_and_throws()
    {
        $user = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'payer_id' => $user->id,
            'payee_id' => $user->id,
            'amount' => 25,
            'status' => TransactionStatus::Failed->value,
            'description' => 'Depósito',
        ]);

        $authMock = Mockery::mock(AuthorizationService::class);
        $authMock->shouldReceive('authorize')->once()->andReturn(false);

        $this->app->instance(AuthorizationService::class, $authMock);

        $this->expectException(UnauthorizedTransferException::class);

        $service = $this->app->make(DepositService::class);
        $service->retryDeposit($transaction);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => TransactionStatus::Failed->value,
        ]);
    }
}
