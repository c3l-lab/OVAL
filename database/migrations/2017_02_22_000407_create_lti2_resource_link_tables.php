<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLti2ResourceLinkTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti2_resource_link', function (Blueprint $table) {
            $table->increments('resource_link_pk');
            $table->integer('context_pk')->unsigned()->nullable()->index();
            $table->integer('consumer_pk')->unsigned()->nullable()->index();
            $table->string('lti_resource_link_id', 255);
            $table->text('settings')->nullable();
            $table->integer('primary_resource_link_pk')->unsigned()->nullable();
            $table->boolean('share_approved')->nullable();
            $table->dateTime('created');
            $table->dateTime('updated');

            $table->foreign('context_pk')->references('context_pk')->on('lti2_context');
            $table->foreign('primary_resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
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
        Schema::table('lti2_resource_link', function(Blueprint $table) {
            $table->dropForeign(['context_pk']);
            $table->dropForeign(['primary_resource_link_pk']);
            $table->dropForeign(['consumer_pk']);
        });
        Schema::dropIfExists('lti2_resource_link');
    }
}
