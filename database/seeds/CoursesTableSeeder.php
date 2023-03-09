<?php

use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('courses')->insert([
            'id'=> 10000000,
        	'name' => 'Test Course 1',
            'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('courses')->insert([
            'id'=> 10000001,
        	'name' => 'Test Course 2',
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }
}
