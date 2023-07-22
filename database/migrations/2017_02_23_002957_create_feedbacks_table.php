<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('point_id')->length(10)->unsigned();
            $table->integer('comment_id')->length(10)->unsigned();
            $table->integer('answer')->nullable();
            $table->timestamps();
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->foreign('point_id')->references('id')->on('points')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['point_id']);
            $table->dropForeign(['comment_id']);
        });
        Schema::dropIfExists('feedbacks');
    }
}
