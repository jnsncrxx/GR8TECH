<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds soft-delete support to accounts so that deleted user accounts
     * can be restored (Activity Logs "restore" action), instead of being
     * permanently removed on every delete.
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
