<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfidenceLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confidence_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id')->length(10)->unsigned();
			$table->integer('level')->nullable()->comment('5=very high, 4=high, 3=medium, 2=low, 1=very low');
            $table->timestamps();
        });
        
        Schema::table('confidence_levels', function(Blueprint $table) {
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('confidence_levels',function(Blueprint $table){
            $table->dropForeign(['comment_id']);
        });
        Schema::dropIfExists('confidence_levels');
    }
}
