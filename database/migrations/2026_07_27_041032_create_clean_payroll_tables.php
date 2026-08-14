<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('employee_id')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Guarded intentionally — this migration must never drop the real
        // employees table. If it ever created a bare placeholder table
        // itself, drop it manually instead.
    }
};