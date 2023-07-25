<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLti2UserResultTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti2_user_result', function (Blueprint $table) {
            $table->increments('user_pk');
            $table->integer('resource_link_pk')->unsigned()->index();
            $table->string('lti_user_id', 255);
            $table->string('lti_result_sourcedid', 1024);
            $table->dateTime('created');
            $table->dateTime('updated');

            $table->foreign('resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lti2_user_result', function (Blueprint $table) {
            $table->dropForeign(['resource_link_pk']);
        });
        Schema::dropIfExists('lti2_user_result');
    }
}
