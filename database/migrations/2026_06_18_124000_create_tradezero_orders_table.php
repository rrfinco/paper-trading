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
        Schema::create('tradezero_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account');
            $table->string('client_order_id')->unique();
            $table->string('symbol');
            $table->string('side');
            $table->integer('quantity');
            $table->string('order_type');
            $table->decimal('limit_price', 15, 2)->nullable();
            $table->string('status')->default('Pending');
            $table->decimal('price_avg', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tradezero_orders');
    }
};
