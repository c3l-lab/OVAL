<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quiz_result', function (Blueprint $table) {
            $table->unsignedInteger('group_video_id')->nullable();

            $table->foreign('group_video_id')->references('id')->on('group_videos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_result', function (Blueprint $table) {
            $table->dropForeign(['group_video_id']);
            $table->dropColumn('group_video_id');
        });
    }
};
