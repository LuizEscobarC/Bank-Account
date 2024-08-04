<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_bank_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('sender_id')->nullable();
            $table->foreign('sender_id')->references('id')->on('account_banks')->cascadeOnDelete();

            $table->uuid('recipient_id')->nullable();
            $table->foreign('recipient_id')->references('id')->on('account_banks')->cascadeOnDelete();

            $table->decimal('amount', 15, 2);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_account_transactions');
    }
};
