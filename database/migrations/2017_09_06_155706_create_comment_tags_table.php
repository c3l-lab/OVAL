<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id')->length(10)->unsigned();
            $table->integer('tag_id')->length(10)->unsigned();
            $table->timestamps();
        });

        Schema::table('comment_tags', function (Blueprint $table) {
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comment_tags', function (Blueprint $table) {
            $table->dropForeign(['comment_id']);
            $table->dropForeign(['tag_id']);
        });
        Schema::dropIfExists('comment_tags');
    }
}
