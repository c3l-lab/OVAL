<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transcripts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_id')->length(10)->unsigned();
            $table->json('transcript')->nullable();
            $table->json('analysis')->nullable();
            $table->timestamp('event_time')->useCurrent();
        });

        Schema::table('transcripts', function (Blueprint $table) {
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transcripts', function (Blueprint $table) {
            $table->dropForeign(['video_id']);
        });
        Schema::dropIfExists('transcripts');
    }
}
