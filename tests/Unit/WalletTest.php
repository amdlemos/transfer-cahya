<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function wallet_is_automatically_created_when_user_is_created()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(Wallet::class, $user->wallet);
        $this->assertEquals(0, $user->wallet->balance);
    }

    #[Test]
    public function wallet_belongs_to_a_user()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user->wallet->user);
        $this->assertEquals($user->id, $user->wallet->user->id);
    }

    #[Test]
    public function it_can_check_sufficient_balance()
    {
        $user = User::factory()->create();
        $user->wallet->update(['balance' => 100.00]);

        $this->assertTrue($user->wallet->hasSufficientBalance(50.00));
        $this->assertTrue($user->wallet->hasSufficientBalance(100.00));
        $this->assertFalse($user->wallet->hasSufficientBalance(100.01));
        $this->assertFalse($user->wallet->hasSufficientBalance(150.00));
    }

    #[Test]
    public function it_can_credit_balance()
    {
        $user = User::factory()->create();
        $initialBalance = $user->wallet->balance;

        $user->wallet->credit(50.00);

        $this->assertEquals($initialBalance + 50.00, $user->wallet->fresh()->balance);
    }

    #[Test]
    public function it_can_debit_balance_when_sufficient()
    {
        $user = User::factory()->create();
        $user->wallet->update(['balance' => 100.00]);

        $user->wallet->debit(30.00);

        $this->assertEquals(70.00, $user->wallet->fresh()->balance);
    }

    #[Test]
    public function it_throws_exception_when_debiting_insufficient_balance()
    {
        $user = User::factory()->create();
        $user->wallet->update(['balance' => 50.00]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Saldo insuficiente');

        $user->wallet->debit(100.00);
    }

    #[Test]
    public function balance_is_cast_to_decimal_with_two_places()
    {
        $user = User::factory()->create();
        $user->wallet->update(['balance' => 123.456]);

        $wallet = $user->wallet->fresh();

        $this->assertIsString($wallet->balance);
        $this->assertEquals('123.46', $wallet->balance);
    }

    #[Test]
    public function multiple_credits_accumulate_correctly()
    {
        $user = User::factory()->create();

        $user->wallet->credit(10.50);
        $user->wallet->credit(20.75);
        $user->wallet->credit(5.25);

        $this->assertEquals(36.50, $user->wallet->fresh()->balance);
    }

    #[Test]
    public function multiple_debits_subtract_correctly()
    {
        $user = User::factory()->create();
        $user->wallet->update(['balance' => 100.00]);

        $user->wallet->debit(10.00);
        $user->wallet->debit(20.00);
        $user->wallet->debit(15.50);

        $this->assertEquals(54.50, $user->wallet->fresh()->balance);
    }

    #[Test]
    public function wallet_is_deleted_when_user_is_deleted()
    {
        $user = User::factory()->create();
        $walletId = $user->wallet->id;

        $user->delete();

        $this->assertDatabaseMissing('wallets', ['id' => $walletId]);
    }
}
