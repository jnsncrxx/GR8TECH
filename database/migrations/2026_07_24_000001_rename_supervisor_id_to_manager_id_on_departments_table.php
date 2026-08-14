<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Renames departments.supervisor_id -> manager_id. The Manager role
     * (Account.role = 'manager') is scoped to a department via this
     * column, so "supervisor" and "manager" were always the same concept —
     * this just makes the column name match the vocabulary used everywhere
     * else (roles, permissions, UI).
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->renameColumn('supervisor_id', 'manager_id');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->renameColumn('manager_id', 'supervisor_id');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('supervisor_id')->references('id')->on('employees')->nullOnDelete();
        });
    }
};
