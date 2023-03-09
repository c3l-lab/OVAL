<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLti2NonceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti2_nonce', function (Blueprint $table) {
            $table->integer('consumer_pk')->unsigned();
            $table->string('value', 32);
            $table->dateTime('expires');

            $table->primary(['consumer_pk', 'value']);
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
        Schema::table('lti2_nonce', function(Blueprint $table) {
            $table->dropForeign(['consumer_pk']);
        });
        Schema::dropIfExists('lti2_nonce');
    }
}
