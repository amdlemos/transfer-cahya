<?php

namespace Tests\Unit;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_transaction_with_required_fields()
    {
        $payer = User::factory()->common()->create();
        $payee = User::factory()->create();

        $transaction = Transaction::create([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => 100.00,
            'status' => TransactionStatus::Pending,
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => 100.00,
        ]);
    }

    #[Test]
    public function it_belongs_to_payer_and_payee()
    {
        $payer = User::factory()->common()->create();
        $payee = User::factory()->merchant()->create();

        $transaction = Transaction::create([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => 50.00,
            'status' => TransactionStatus::Pending,
        ]);

        $this->assertInstanceOf(User::class, $transaction->payer);
        $this->assertInstanceOf(User::class, $transaction->payee);
        $this->assertEquals($payer->id, $transaction->payer->id);
        $this->assertEquals($payee->id, $transaction->payee->id);
    }

    #[Test]
    public function status_is_cast_to_enum()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::Completed,
        ]);

        $this->assertInstanceOf(TransactionStatus::class, $transaction->status);
        $this->assertTrue($transaction->status === TransactionStatus::Completed);
    }

    #[Test]
    public function amount_is_cast_to_decimal_with_two_places()
    {
        $transaction = Transaction::factory()->create([
            'amount' => 123.456,
        ]);

        $this->assertIsString($transaction->amount);
        $this->assertEquals('123.46', $transaction->amount);
    }

    #[Test]
    public function it_can_check_if_transaction_is_completed()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::Completed,
        ]);

        $this->assertTrue($transaction->isCompleted());
        $this->assertFalse($transaction->isPending());
        $this->assertFalse($transaction->isFailed());
    }

    #[Test]
    public function it_can_check_if_transaction_is_pending()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::Pending,
        ]);

        $this->assertTrue($transaction->isPending());
        $this->assertFalse($transaction->isCompleted());
    }

    #[Test]
    public function it_can_check_if_transaction_is_failed()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::Failed,
        ]);

        $this->assertTrue($transaction->isFailed());
        $this->assertFalse($transaction->isCompleted());
    }

    #[Test]
    public function it_can_mark_transaction_as_completed()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::Pending,
        ]);

        $transaction->markAsCompleted();

        $this->assertTrue($transaction->fresh()->isCompleted());
    }

    #[Test]
    public function it_can_mark_transaction_as_failed()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::Pending,
        ]);

        $transaction->markAsFailed();

        $this->assertTrue($transaction->fresh()->isFailed());
    }

    #[Test]
    public function scope_completed_filters_only_completed_transactions()
    {
        Transaction::factory()->create(['status' => TransactionStatus::Completed]);
        Transaction::factory()->create(['status' => TransactionStatus::Pending]);
        Transaction::factory()->create(['status' => TransactionStatus::Failed]);

        $completed = Transaction::completed()->get();

        $this->assertCount(1, $completed);
        $this->assertTrue($completed->first()->isCompleted());
    }

    #[Test]
    public function scope_pending_filters_only_pending_transactions()
    {
        Transaction::factory()->create(['status' => TransactionStatus::Completed]);
        Transaction::factory()->create(['status' => TransactionStatus::Pending]);
        Transaction::factory()->create(['status' => TransactionStatus::Pending]);

        $pending = Transaction::pending()->get();

        $this->assertCount(2, $pending);
    }

    #[Test]
    public function scope_failed_filters_only_failed_transactions()
    {
        Transaction::factory()->create(['status' => TransactionStatus::Completed]);
        Transaction::factory()->create(['status' => TransactionStatus::Failed]);

        $failed = Transaction::failed()->get();

        $this->assertCount(1, $failed);
        $this->assertTrue($failed->first()->isFailed());
    }

    #[Test]
    public function scope_for_user_returns_transactions_where_user_is_payer_or_payee()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Transaction::factory()->create(['payer_id' => $user->id]);
        Transaction::factory()->create(['payee_id' => $user->id]);
        Transaction::factory()->create(['payer_id' => $otherUser->id, 'payee_id' => $otherUser->id]);

        $userTransactions = Transaction::forUser($user->id)->get();

        $this->assertCount(2, $userTransactions);
    }

    #[Test]
    public function scope_sent_by_returns_only_transactions_sent_by_user()
    {
        $user = User::factory()->create();

        Transaction::factory()->create(['payer_id' => $user->id]);
        Transaction::factory()->create(['payer_id' => $user->id]);
        Transaction::factory()->create(['payee_id' => $user->id]);

        $sentTransactions = Transaction::sentBy($user->id)->get();

        $this->assertCount(2, $sentTransactions);
    }

    #[Test]
    public function scope_received_by_returns_only_transactions_received_by_user()
    {
        $user = User::factory()->create();

        Transaction::factory()->create(['payer_id' => $user->id]);
        Transaction::factory()->create(['payee_id' => $user->id]);
        Transaction::factory()->create(['payee_id' => $user->id]);

        $receivedTransactions = Transaction::receivedBy($user->id)->get();

        $this->assertCount(2, $receivedTransactions);
    }

    #[Test]
    public function description_is_optional()
    {
        $transaction = Transaction::factory()->create([
            'description' => null,
        ]);

        $this->assertNull($transaction->description);
    }

    #[Test]
    public function transaction_can_have_description()
    {
        $description = 'Pagamento de serviço';

        $transaction = Transaction::factory()->create([
            'description' => $description,
        ]);

        $this->assertEquals($description, $transaction->description);
    }
}
