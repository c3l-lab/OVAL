<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->string('ref_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->dropColumn(['ref_id', 'ref_type']);
        });
    }
};
