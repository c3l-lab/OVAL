<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLtiCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti_credentials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('db_type');
            $table->string('host');
            $table->unsignedSmallInteger('port');
            $table->string('database');
            $table->string('username'); //encrypt
            $table->string('password'); //encrypt
            $table->string('prefix')->nullable();
            $table->integer('consumer_id')->length(10)->unsigned();
            $table->timestamps();
        });

        Schema::table('lti_credentials', function (Blueprint $table) {
            $table->foreign('consumer_id')->references('consumer_pk')->on('lti2_consumer')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lti_credentials', function (Blueprint $table) {
            $table->dropForeign(['consumer_id']);
        });
        Schema::dropIfExists('lti_credentials');
    }
}
