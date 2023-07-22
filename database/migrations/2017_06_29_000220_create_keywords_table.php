<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keywords', function (Blueprint $table) {
            $table->increments('id');
            $table->string('keyword');
            $table->integer('videoId')->length(10)->unsigned();
            $table->float('startTime', 7, 2)->nullable();
            $table->float('endTime', 7, 2)->nullable();
            $table->decimal('relevance', 9, 8)->nullable();
            $table->string('type');
        });
        Schema::table('keywords', function (Blueprint $table) {
            $table->foreign('videoId')->references('id')->on('videos')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropForeign(['videoId']);
        });
        Schema::dropIfExists('keywords');
    }
}
