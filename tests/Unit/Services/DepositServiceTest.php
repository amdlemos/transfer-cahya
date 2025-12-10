<?php

namespace Tests\Unit\Services;

use App\Enums\TransactionStatus;
use App\Services\AuthorizationService;
use App\Services\DepositService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class DepositServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_deposit_success_credits_wallet_and_completes_transaction()
    {
        $user = User::factory()->create();

        $authMock = Mockery::mock(AuthorizationService::class);
        $authMock->shouldReceive('authorize')->once()->andReturn(true);

        $this->app->instance(AuthorizationService::class, $authMock);

        $service = $this->app->make(DepositService::class);

        $transaction = $service->deposit($user, 100.0);

        $this->assertEquals(100.0, (float) $user->wallet->fresh()->balance);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => TransactionStatus::Completed->value,
        ]);
    }
}
