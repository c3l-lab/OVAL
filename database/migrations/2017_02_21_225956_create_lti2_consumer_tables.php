<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLti2ConsumerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti2_consumer', function (Blueprint $table) {
            $table->increments('consumer_pk');
            $table->string('name', 50);
            $table->string('consumer_key256', 256)->unique();
            $table->text('consumer_key')->nullable();
            $table->string('secret', 1024);
            $table->string('lti_version', 10)->nullable();
            $table->string('consumer_name', 255)->nullable();
            $table->string('consumer_version', 255)->nullable();
            $table->string('consumer_guid', 1024)->nullable();
            $table->text('profile')->nullable();
            $table->text('tool_proxy')->nullable();
            $table->text('settings')->nullable();
            $table->boolean('protected');
            $table->boolean('enabled');
            $table->dateTime('enable_from')->nullable();
            $table->dateTime('enable_until')->nullable();
            $table->date('last_access')->nullable();
            $table->dateTime('created');
            $table->dateTime('updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lti2_consumer');
    }
}
