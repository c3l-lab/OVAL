<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $courses = oval\Models\Course::all();
        foreach ($courses as $c) {
            DB::table('groups')->insert([
                'name' => $c->name.' - Default Group',
                'course_id' => $c->id,
            ]);
            DB::table('groups')->insert([
                'name' => $c->name.' - Partial Group',
                'course_id' => $c->id,
            ]);
        }

    }
}
