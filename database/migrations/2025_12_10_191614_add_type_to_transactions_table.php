<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('type')->default('transfer')->after('status');
            $table->index('type');
        });

        // Atualizar transações existentes com o tipo baseado na lógica de negócio
        DB::statement("
            UPDATE transactions 
            SET type = CASE
                WHEN payer_id != payee_id THEN 'transfer'
                WHEN payer_id = payee_id AND description = 'Depósito' THEN 'deposit'
                WHEN description = 'Saque' THEN 'withdrawal'
                ELSE 'transfer'
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
