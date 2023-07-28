<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PointInstructionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('point_instructions')->insert([
            'group_video_id'=>5,
            'description'=>'Please ensure you include in your answer: how green the grass is, how deep the ocean is, and why the chicken crossed the road.',
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }
}
