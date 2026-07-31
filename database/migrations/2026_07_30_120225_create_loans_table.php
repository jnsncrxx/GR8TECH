<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->uuid('loan_type_id');
            $table->uuid('company_id')->nullable();

            $table->decimal('principal_amount', 12, 2);
            // Copied from the loan type at request time so a later change to
            // the type's default rate never retroactively alters an existing
            // loan's terms.
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->enum('interest_type', ['flat', 'diminishing'])->default('flat');
            $table->unsignedSmallInteger('term_months');
            // Amount deducted per payroll cutoff, computed at approval time.
            $table->decimal('amortization_amount', 12, 2)->default(0);
            $table->decimal('total_repayable', 12, 2)->default(0);
            $table->decimal('remaining_balance', 12, 2)->default(0);

            $table->date('start_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');

            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->uuid('requested_by')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('loan_type_id')->references('id')->on('loan_types')->onDelete('restrict');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('accounts')->onDelete('set null');

            $table->index(['employee_id', 'status']);
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};