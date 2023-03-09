<?php

use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('comments')->insert([
        	'group_video_id'=>1,
        	'user_id'=>10000001,
        	'description'=>'Nunc condimentum, arcu vitae commodo fermentum, libero nibh accumsan lectus, sit amet hendrerit felis mi et metus. Quisque sodales arcu at suscipit consectetur. ',
        	// 'tags'=>'tag1',
			'privacy'=>"all",
			'created_at' => date("Y-m-d H:i:s"),
			'updated_at' => date("Y-m-d H:i:s")
        ]);
		DB::table('comments')->insert([
        	'group_video_id'=>1,
        	'user_id'=>10000001,
        	'description'=>'woof woof woof woooooffff!',
        	// 'tags'=>'tag1,tag2,tag3',
        	'privacy'=>"all",
        	'created_at' => date("Y-m-d H:i:s"),
			'updated_at' => date("Y-m-d H:i:s")
        ]);
		DB::table('comments')->insert([
        	'group_video_id'=>2,
        	'user_id'=>10000001,
        	'description'=>'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.',
        	'privacy'=>"all",
        	'created_at' => date("Y-m-d H:i:s"),
			'updated_at' => date("Y-m-d H:i:s")
        ]);
		DB::table('comments')->insert([
        	'group_video_id'=>5,
        	'user_id'=>10000001,
        	'description'=>'Hello!',
        	'privacy'=>"all",
        	'created_at' => date("Y-m-d H:i:s"),
			'updated_at' => date("Y-m-d H:i:s")
        ]);
    }
}
