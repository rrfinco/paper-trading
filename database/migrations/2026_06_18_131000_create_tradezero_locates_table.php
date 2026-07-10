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
        Schema::create('tradezero_locates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account');
            $table->string('symbol');
            $table->integer('quantity');
            $table->string('quote_req_id')->unique();
            $table->integer('locate_status'); // 50=Filled, 54=Pending, 56=Rejected, 65=Offered, 67=Expired
            $table->decimal('locate_price', 15, 4)->default(0.0000);
            $table->integer('locate_type'); // 3=Pre-Borrow, 4=Single Use
            $table->integer('available_quantity')->default(0); // quantity available to short
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tradezero_locates');
    }
};
