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
        // This legacy migration predates the accounts-table creation file.
        // Fresh databases add these columns in the create migration instead.
        if (! Schema::hasTable('accounts')) {
            return;
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('google_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('accounts')) {
            return;
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar']);
        });
    }
};
