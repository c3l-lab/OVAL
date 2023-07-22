<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_video_id')->length(10)->unsigned();
            $table->integer('user_id')->length(10)->unsigned();
            $table->double('start_time')->unsigned();
            $table->text('description');
            $table->enum("privacy", ['private', 'all', 'nominated'])
                    ->default("all");
            $table->json('visible_to')
                    ->nullable()
                    ->comment("array of user_id of nominated students if privacy is set to 'nominated'");
            $table->enum('status', ['current', 'archived', 'deleted'])
                    ->default('current');
            $table->timestamps();
        });

        Schema::table('annotations', function (Blueprint $table) {
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
        Schema::table('annotations', function (Blueprint $table) {
            $table->dropForeign(['group_video_id']);
        });
        Schema::dropIfExists('annotations');
    }
}
