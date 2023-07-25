<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_video_id')->length(10)->unsigned();
            $table->integer('user_id')->length(10)->unsigned();
            $table->text('description')->nullable();
            $table->enum("privacy", ['private', 'all', 'nominated'])
                    ->default("all");
            $table->json('visible_to')
                    ->nullable()
                    ->comment("array of user_id of nominated students if privacy is set to 'nominated'");
            $table->enum('status', ['current', 'archived', 'deleted'])
                    ->default('current');
            $table->integer('parent')->length(10)->unsigned()->nullable()->default(null);
            $table->timestamps();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('group_video_id')->references('id')->on('group_videos')->onDelete('cascade');
            $table->foreign('parent')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['group_video_id']);
            $table->dropForeign(['parent']);
        });
        Schema::dropIfExists('comments');
    }
}
