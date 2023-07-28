<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CommentInstructionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $v = oval\Models\Video::find(2);
        $gv = oval\Models\GroupVideo::where('video_id', '=', $v->id)
                ->first();

        DB::table('comment_instructions')->insert([
            'group_video_id'=>$gv->id,
            'description'=>'Please summarise this video'
        ]);
    }
}
