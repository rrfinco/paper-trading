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
        Schema::create('tradezero_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account');
            $table->string('account_status')->default('Active');
            $table->string('account_type')->default('Paper');
            $table->decimal('available_cash', 15, 2)->default(0.00);
            $table->decimal('available_cash_ems', 15, 2)->default(0.00);
            $table->decimal('buying_power', 15, 2)->default(0.00);
            $table->decimal('equity', 15, 2)->default(0.00);
            $table->boolean('is_future_account')->default(false);
            $table->decimal('leverage', 8, 2)->default(0.00);
            $table->decimal('maintenance_deficit', 15, 2)->default(0.00);
            $table->decimal('margin_deficit', 15, 2)->default(0.00);
            $table->decimal('margin_ratio', 8, 2)->default(0.00);
            $table->decimal('margin_requirement', 15, 2)->default(0.00);
            $table->integer('opt_contracts_traded')->default(0);
            $table->integer('opt_level')->default(0);
            $table->decimal('option_cash_total_balance', 15, 2)->default(0.00);
            $table->integer('option_trading_level')->default(0);
            $table->decimal('overnight_bp', 15, 2)->default(0.00);
            $table->decimal('realized', 15, 2)->default(0.00);
            $table->integer('shares_traded')->default(0);
            $table->decimal('sod_equity', 15, 2)->default(0.00);
            $table->decimal('total_commissions', 15, 2)->default(0.00);
            $table->decimal('total_locate_costs', 15, 2)->default(0.00);
            $table->decimal('unrealized', 15, 2)->default(0.00);
            $table->decimal('used_leverage', 8, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tradezero_accounts');
    }
};
