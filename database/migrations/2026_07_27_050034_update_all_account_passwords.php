<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Intentionally left blank. This migration previously replaced every
        // account password with one shared development password. Credential
        // changes must not run as part of production schema migrations; use a
        // local seeder or the password-reset workflow for demo accounts.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No schema or data changes to reverse.
    }
};
