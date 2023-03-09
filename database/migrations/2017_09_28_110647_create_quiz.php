<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuiz extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_creation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('creator_id')->unsigned()->index();
            $table->string('identifier');
            $table->string('media_type')->nullable();
            $table->json('quiz_data')->nullable();
            $table->boolean('visable');
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
        Schema::dropIfExists('quiz_creation');
    }
}
