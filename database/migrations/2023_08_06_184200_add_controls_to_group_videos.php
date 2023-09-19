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
        Schema::table('group_videos', function (Blueprint $table) {
            $table->json('controls');
        });
        \DB::statement('UPDATE group_videos SET controls = \'{"fullscreen":true,"captions":true,"speed":true,"play":true,"progress":true,"volume":true}\'');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_videos', function (Blueprint $table) {
            $table->dropColumn('controls');
        });
    }
};
