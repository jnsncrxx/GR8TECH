<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // TIMESTAMP columns in MySQL can silently get "update this to right now"
    // behavior if there's no explicit default - that's what was corrupting
    // time_in every time someone clocked out. DATETIME columns never do
    // this, so switching fixes it for good.
    public function up(): void
    {
        DB::statement('ALTER TABLE time_entries MODIFY time_in DATETIME NOT NULL');
        DB::statement('ALTER TABLE time_entries MODIFY time_out DATETIME NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE time_entries MODIFY time_in TIMESTAMP NOT NULL');
        DB::statement('ALTER TABLE time_entries MODIFY time_out TIMESTAMP NULL');
    }
};