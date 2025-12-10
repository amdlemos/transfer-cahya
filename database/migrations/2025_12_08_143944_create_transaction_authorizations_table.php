<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_authorizations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->boolean('authorized')->default(false);
            $table->text('response')->nullable();
            $table->timestamp('attempted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_authorizations');
    }
};
