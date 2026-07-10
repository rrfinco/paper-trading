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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->decimal('equity', 15, 2)->default(0.00);
            $table->string('account_type')->default('paper'); // 'paper' or 'live'
            $table->string('provider')->default('paper'); // 'paper', 'tradezero', 'alpaca', etc.
            $table->string('status')->default('active'); // 'active', 'inactive'
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 20);
            $table->string('side', 10); // 'Buy', 'Sell', 'Short', 'Cover'
            $table->integer('quantity');
            $table->integer('filled_quantity')->default(0);
            $table->string('order_type', 10); // 'Market', 'Limit', 'Stop', etc.
            $table->string('security_type', 20)->default('Stock'); // 'Stock', 'Option', 'Mleg'
            $table->decimal('limit_price', 15, 4)->nullable();
            $table->decimal('stop_price', 15, 4)->nullable();
            $table->string('status', 20)->default('new'); // 'new', 'pending', 'filled', 'cancelled', 'rejected'
            $table->json('legs')->nullable(); // Multi-leg strategies
            $table->string('provider', 20); // 'paper', 'tradezero', etc.
            $table->string('broker_order_id')->nullable(); // ID returned by the broker API
            $table->string('client_order_id')->unique();
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 20);
            $table->integer('quantity'); // Positive for Long, negative for Short
            $table->decimal('avg_price', 15, 4)->default(0.0000);
            $table->timestamps();
            
            $table->unique(['account_id', 'symbol']);
        });

        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 20);
            $table->integer('qty');
            $table->decimal('fill_price', 15, 4);
            $table->decimal('commission', 8, 2)->default(0.00);
            $table->timestamp('executed_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('accounts');
    }
};
