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
            $table->json('annotation_config');
        });

        \DB::statement('UPDATE group_videos SET annotation_config = \'{"label":"New Annotation","header_name":"ADD ANNOTATION","downloadable":true,"is_show_annotation_button":true,"enable_structured_annotation_quiz": true}\'');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_videos', function (Blueprint $table) {
            $table->dropColumn('annotation_config');
        });
    }
};
