<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLti2ToolProxyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti2_tool_proxy', function (Blueprint $table) {
            $table->increments('tool_proxy_pk');
            $table->string('tool_proxy_id', 32)->unique();
            $table->integer('consumer_pk')->unsigned()->index();
            $table->text('tool_proxy');
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
        Schema::table('lti2_tool_proxy', function(Blueprint $table) {
            $table->dropForeign(['consumer_pk']);
        });
        Schema::dropIfExists('lti2_tool_proxy');
    }
}
