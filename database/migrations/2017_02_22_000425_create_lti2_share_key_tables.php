<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLti2ShareKeyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti2_share_key', function (Blueprint $table) {
            $table->string('share_key_id', 32);
            $table->integer('resource_link_pk')->unsigned()->index();
            $table->boolean('auto_approve');
            $table->dateTime('expires');

            $table->primary('share_key_id');
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
        Schema::table('lti2_share_key', function (Blueprint $table) {
            $table->dropPrimary(['share_key_id']);
            $table->dropForeign(['resource_link_pk']);
        });
        Schema::dropIfExists('lti2_share_key');
    }
}
