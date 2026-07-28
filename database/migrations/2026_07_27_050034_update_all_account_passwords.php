<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('accounts')->update([
        'password' => Hash::make('password')
    ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no automatic rollback – you’d need to restore original hashes manually
    }
};
