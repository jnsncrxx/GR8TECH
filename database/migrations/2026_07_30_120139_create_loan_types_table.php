<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            // Flat: total repayable = principal * (1 + rate/100), split evenly.
            // Diminishing: interest recalculated on the remaining balance each
            // payment - not yet implemented in the amortization calculator
            // below (see Loan::computeAmortization()), stored for future use.
            $table->decimal('default_interest_rate', 5, 2)->default(0);
            $table->enum('interest_type', ['flat', 'diminishing'])->default('flat');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_types');
    }
};