<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLti2Lti2ContextTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti2_context', function (Blueprint $table) {
            $table->increments('context_pk');
            $table->integer('consumer_pk')->unsigned()->index();
            $table->string('lti_context_id', 255);
            $table->text('settings')->nullable();
            $table->dateTime('created');
            $table->dateTime('updated');

            $table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lti2_context', function(Blueprint $table) {
            $table->dropForeign(['consumer_pk']);
        });
        Schema::dropIfExists('lti2_context');
    }
}
