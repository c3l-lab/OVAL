<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->integer('video_id')->unsigned();
            $table->integer('moodle_resource_id')->nullable();
            $table->boolean('hide');
            $table->integer('order')->unsigned()->default(1000);
            $table->boolean('show_analysis');
            $table->enum('status', ['current', 'archived'])->default('current');
            $table->timestamps();
        });

        Schema::table('group_videos', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
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
        Schema::table('group_videos', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['video_id']);
        });

        Schema::dropIfExists('group_videos');
    }
}
