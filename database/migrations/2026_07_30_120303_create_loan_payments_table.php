<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id');
            // Nullable so a manual/off-cycle payment (e.g. early payoff) can
            // be recorded without requiring a payroll row to exist.
            $table->uuid('payroll_id')->nullable();

            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->decimal('balance_after', 12, 2);

            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('accounts')->onDelete('set null');

            $table->index('loan_id');
            $table->index('payroll_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};