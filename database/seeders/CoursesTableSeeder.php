<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use oval\Models\Course;
use oval\Models\Group;
use oval\Models\Video;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Course::factory()->has(
            Group::factory()->has(
                Video::factory()->count(1)
            )->count(1)
        )->create();
    }
}
