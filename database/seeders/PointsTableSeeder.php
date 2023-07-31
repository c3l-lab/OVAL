<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PointsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('points')->insert([
            'id'=>1,
            'group_video_id'=>5,
            'description'=>'Grass is green.',
            'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('points')->insert([
            'id'=>2,
            'group_video_id'=>5,
            'description'=>'Ocean depth.',
            'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('points')->insert([
            'id'=>3,
            'group_video_id'=>5,
            'description'=>'Chicken crossed the road.',
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }
}
