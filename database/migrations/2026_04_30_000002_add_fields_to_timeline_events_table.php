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
        Schema::table('timeline_events', function (Blueprint $table) {
            $table->string('icon_type')->default('star')->after('description');
            $table->string('color')->default('amber')->after('icon_type');
            $table->string('date_label')->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timeline_events', function (Blueprint $table) {
            $table->dropColumn(['icon_type', 'color', 'date_label']);
        });
    }
};
