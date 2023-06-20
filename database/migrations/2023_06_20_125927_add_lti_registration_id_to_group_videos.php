<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use oval\LtiRegistration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('group_videos', function (Blueprint $table) {
            $table->foreignIdFor(LtiRegistration::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_videos', function (Blueprint $table) {
            $table->dropForeignIdFor(LtiRegistration::class);
        });
    }
};
