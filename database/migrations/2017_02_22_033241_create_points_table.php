<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_video_id')->length(10)->unsigned();
            $table->string('description');
            $table->boolean('is_course_wide')->default(false);
            $table->timestamps();
        });

        Schema::table('points', function (Blueprint $table) {
            $table->foreign('group_video_id')->references('id')->on('group_videos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropForeign(['group_video_id']);
        });
        Schema::dropIfExists('points');
    }
}
