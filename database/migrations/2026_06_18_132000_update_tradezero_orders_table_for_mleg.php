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
        Schema::table('tradezero_orders', function (Blueprint $table) {
            $table->string('security_type')->default('Stock')->after('order_type');
            $table->json('legs')->nullable()->after('security_type');
            $table->string('side')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tradezero_orders', function (Blueprint $table) {
            $table->dropColumn(['security_type', 'legs']);
            $table->string('side')->nullable(false)->change();
        });
    }
};
