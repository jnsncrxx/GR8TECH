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
        Schema::create('schedule_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable();
            $table->string('code', 20);
            $table->string('name');
            $table->enum('schedule_type', ['fixed', 'flexible'])->default('fixed');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('required_hours', 4, 2)->default(8.00);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('accounts')->onDelete('set null');

            $table->index('company_id');
            // A code like "A1" only needs to be unique within its own company -
            // two different companies can each have their own "A1".
            $table->unique(['company_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_templates');
    }
};