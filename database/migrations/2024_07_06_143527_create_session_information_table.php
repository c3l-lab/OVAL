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
        Schema::create('session_information', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('os')->nullable();
            $table->integer('doc_width')->nullable();
            $table->integer('doc_height')->nullable();
            $table->string('layout')->nullable();
            $table->integer('init_screen_width')->nullable();
            $table->integer('init_screen_height')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_information');
    }
};
