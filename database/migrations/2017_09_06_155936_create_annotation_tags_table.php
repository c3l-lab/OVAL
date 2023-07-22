<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotation_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('annotation_id')->length(10)->unsigned();
            $table->integer('tag_id')->length(10)->unsigned();
            $table->timestamps();
        });
        Schema::table('annotation_tags', function (Blueprint $table) {
            $table->foreign('annotation_id')->references('id')->on('annotations')->onDelete('cascade');
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
        Schema::table('annotation_tags', function (Blueprint $table) {
            $table->dropForeign(['annotation_id']);
            $table->dropForeign(['tag_id']);
        });
        Schema::dropIfExists('annotation_tags');
    }
}
