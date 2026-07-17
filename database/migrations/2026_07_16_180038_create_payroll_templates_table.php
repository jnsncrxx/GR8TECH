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
        Schema::create('payroll_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('monthly_rate', 10, 2)->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('overtime_rate', 10, 2)->nullable();
            $table->decimal('night_differential_rate', 10, 2)->nullable();
            $table->decimal('allowances', 10, 2)->nullable()->default(0);
            $table->decimal('deductions', 10, 2)->nullable()->default(0);
            $table->decimal('sss', 10, 2)->nullable();
            $table->decimal('phic', 10, 2)->nullable();
            $table->decimal('hdmf', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_templates');
    }
};
