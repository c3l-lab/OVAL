<?php

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
        $v = oval\Video::find(2);
        $gv = oval\GroupVideo::where('video_id', '=', $v->id)
                ->first();

        DB::table('comment_instructions')->insert([
            'group_video_id'=>$gv->id,
            'description'=>'Please summarise this video'
        ]);
    }
}
