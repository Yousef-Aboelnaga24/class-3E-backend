<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'student', 'user'])->default('user')->after('password');
            $table->foreignId('class_code_id')->nullable()->after('role')->constrained('class_codes')->nullOnDelete();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_code_id');
            $table->dropColumn('role');
            $table->dropSoftDeletes();
        });
    }
};
