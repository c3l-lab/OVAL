<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AnnotationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('annotations')->insert([
            'group_video_id'=>1,
            'user_id'=>10000001,
            'start_time'=>0,
            'description'=>'test annotation',
            'privacy' => 'all',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('annotations')->insert([
            'group_video_id'=>2,
            'user_id'=>10000001,
            'start_time'=>30,
            'description'=>'test annotation for video 2',
            'privacy' => 'all',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
    }
}
