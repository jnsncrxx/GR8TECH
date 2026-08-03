<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create document_folders table
        Schema::create('document_folders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index('employee_id');
        });

        // Add folder_id to documents table (nullable so existing docs are untouched)
        Schema::table('documents', function (Blueprint $table) {
            $table->uuid('folder_id')->nullable()->after('employee_id');
            $table->foreign('folder_id')->references('id')->on('document_folders')->onDelete('set null');
            $table->index('folder_id');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropIndex(['folder_id']);
            $table->dropColumn('folder_id');
        });

        Schema::dropIfExists('document_folders');
    }
};
