<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_credentials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channel_id');
            $table->string('channel_title');
            $table->string('client_id')->unique();
            $table->string('client_secret');
            $table->json('access_token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_credentials');
    }
}
