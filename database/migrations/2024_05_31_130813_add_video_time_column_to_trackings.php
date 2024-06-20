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
        Schema::table('trackings', function (Blueprint $table) {
            $table->decimal('video_time', 10, 2)->nullable()->after('target');
        });

        \DB::table('trackings')
            ->where('info', 'regexp', '^[0-9]+(\.[0-9]+)?$')
            ->update([
                'video_time' => \DB::raw('CAST(info AS DECIMAL(10,2))'),
                'info' => null
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->dropColumn('video_time');
        });
    }
};
