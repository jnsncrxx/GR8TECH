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
                $table->id();
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