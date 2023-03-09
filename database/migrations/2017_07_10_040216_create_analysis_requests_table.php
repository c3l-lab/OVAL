<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalysisRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analysis_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_id')->length(10)->unsigned();
            $table->integer('user_id')->length(10)->unsigned();
            $table->enum('status', ['pending', 'rejected', 'processing', 'processed', 'deleted'])->default('pending');
            $table->timestamps();
        });

        Schema::table('analysis_requests', function (Blueprint $table) {
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analysis_requests', function(Blueprint $table) {
            $table->dropForeign(['video_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('analysis_requests');
    }
}
