<?php

use Illuminate\Database\Seeder;

class GroupVideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('group_videos')->insert([
            ['group_id'=>1, 'video_id'=>1, 'hide'=>false],
            ['group_id'=>1, 'video_id'=>2, 'hide'=>false],
            ['group_id'=>2, 'video_id'=>3, 'hide'=>false],
            ['group_id'=>2, 'video_id'=>4, 'hide'=>false],
            ['group_id'=>2, 'video_id'=>5, 'hide'=>false],
            ['group_id'=>3, 'video_id'=>6, 'hide'=>false],
            ['group_id'=>4, 'video_id'=>7, 'hide'=>false],
            ['group_id'=>1, 'video_id'=>8, 'hide'=>false],
            ['group_id'=>1, 'video_id'=>9, 'hide'=>false],
            ['group_id'=>1, 'video_id'=>10, 'hide'=>false],
        ]);
    }
}
