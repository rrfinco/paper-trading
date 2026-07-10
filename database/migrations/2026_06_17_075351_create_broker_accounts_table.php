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
        Schema::create('broker_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account_id');
            $table->text('api_key');
            $table->text('api_secret');
            $table->string('status')->default('disconnected');
            $table->string('account_type')->default('paper');
            $table->decimal('buying_power', 15, 2)->default(0.00);
            $table->decimal('cash_balance', 15, 2)->default(0.00);
            $table->decimal('equity', 15, 2)->default(0.00);
            $table->json('trading_permissions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broker_accounts');
    }
};
