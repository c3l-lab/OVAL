<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use oval\Models\LtiConsumer;
use oval\Models\Course;

class EditCoursesTableForMultipleLti extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('consumer_id')->length(10)->unsigned()->nullable();
            $table->integer('moodle_course_id')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
        });



        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('consumer_id')
                    ->references('consumer_pk')->on('lti2_consumer')
                    ->onDelete('cascade');
        });

        //-- assume existing courses to have the course_id from moodle.
        //-- copy course_id to lti_course_id and populate consumer_id with existing lti2_consumer.id
        $consumers = LtiConsumer::all();
        $consumer = null;
        if ($consumers->count() == 1) {
            $consumer = $consumers->first();
        }
        if (!empty($consumer)) {
            $courses = Course::all();
            foreach ($courses as $c) {
                $c->consumer_id = $consumer->consumer_pk;
                $c->moodle_course_id = $c->id;
                $c->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Leave the data as is...
        //If rolled back after there are multiple LTI data,
        //OVAL doesn't work properly.

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['consumer_id']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('consumer_id');
            $table->dropColumn('moodle_course_id');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }
}
