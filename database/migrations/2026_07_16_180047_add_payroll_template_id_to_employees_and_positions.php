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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignUuid('payroll_template_id')->nullable()->constrained('payroll_templates')->onDelete('restrict');
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->foreignUuid('payroll_template_id')->nullable()->constrained('payroll_templates')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['payroll_template_id']);
            $table->dropColumn('payroll_template_id');
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeign(['payroll_template_id']);
            $table->dropColumn('payroll_template_id');
        });
    }
};
