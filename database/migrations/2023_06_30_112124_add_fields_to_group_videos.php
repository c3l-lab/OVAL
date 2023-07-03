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
        Schema::table('group_videos', function (Blueprint $table) {
            $table->boolean('show_comments')->default(true);
            $table->boolean('show_annotations')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_videos', function (Blueprint $table) {
            $table->dropColumn('show_comments');
            $table->dropColumn('show_annotations');
        });
    }
};
