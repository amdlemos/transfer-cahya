<?php

namespace Tests\Unit\Services;

use App\Models\Transaction;
use App\Services\AuthorizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthorizationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_success_records_and_returns_true()
    {
        $transaction = Transaction::factory()->create();

        Http::fake([
            '*' => Http::response(['status' => 'success'], 200),
        ]);

        $service = new AuthorizationService;

        $result = $service->authorize($transaction);

        $this->assertTrue($result);

        $this->assertDatabaseHas('transaction_authorizations', [
            'transaction_id' => $transaction->id,
            'authorized' => 1,
        ]);
    }

    public function test_authorize_failure_records_and_returns_false()
    {
        $transaction = Transaction::factory()->create();

        Http::fake([
            '*' => Http::response(['status' => 'fail'], 200),
        ]);

        $service = new AuthorizationService;

        $result = $service->authorize($transaction);

        $this->assertFalse($result);

        $this->assertDatabaseHas('transaction_authorizations', [
            'transaction_id' => $transaction->id,
            'authorized' => 0,
        ]);
    }
}
