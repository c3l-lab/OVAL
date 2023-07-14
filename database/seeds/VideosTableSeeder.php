<?php

use Illuminate\Database\Seeder;

class VideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('videos')->insert([
        	'identifier' => 'MKoroBS8Ke4',
        	'title' => 'Study with the best - University of South Australia',
        	'description' => 'To be the best in your field, you need a university with the best qualifications; a university that offers a choice of over 200 world-class degrees, that is globally recognised for its teaching, research and facilities, and is the best in South Australia for graduate careers.',
        	'duration' => 30,
        	'thumbnail_url' => 'https://img.youtube.com/vi/MKoroBS8Ke4/1.jpg',
        	'media_type' => 'youtube',
        	'added_by' => 1,
        	'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('videos')->insert([
        	'identifier' => 'PxXp5ZgtbLQ',
        	'title' => 'UniSA 25th birthday campus parties',
        	'description' => 'In March 2016, UniSA celebrated its 25th birthday with staff, students and alumni at the 25th birthday parties. One held per UniSA campus, these birthday parties featured a cupcake decorating stand, fairy bread, party food, music, a giant pass-the-parcel and cake cutting. ',
        	'duration' => 59,
        	'thumbnail_url' => 'https://img.youtube.com/vi/PxXp5ZgtbLQ/1.jpg',
        	'media_type' => 'youtube',
        	'added_by' => 2,
        	'created_at' => date("Y-m-d H:i:s")

        ]);
        DB::table('videos')->insert([
			'identifier' => 'ZuQJbne0ifU',
        	'title' => 'Final Year Engineering Student Project Showcase ',
        	'description' => 'Hear from Head of School: Engineering, Associate Professor Brenton Dansie and final year University of South Australia Engineering students as they showcase their student projects.',
        	'duration' => '98',
        	'thumbnail_url' => 'https://img.youtube.com/vi/ZuQJbne0ifU/1.jpg',
        	'media_type' => 'youtube',
        	'added_by' => 2,
        	'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('videos')->insert([
			'identifier' => 'tbq8YtUJJuk',
        	'title' => 'UniSA Sport – Get Involved ',
        	'description' => 'UniSA Sport is the hub of all sport whether it’s through a club, on campus, out in the community or at representative events.',
        	'duration' => '57',
        	'thumbnail_url' => 'https://img.youtube.com/vi/tbq8YtUJJuk/1.jpg',
        	'media_type' => 'youtube',
        	'added_by' => 2,
    		'created_at' => date("Y-m-d H:i:s")
      	]);
        DB::table('videos')->insert([
			'identifier' => '2Q5gw5bWYWY',
        	'title' => 'UniSA and DXC Technology Dandelion Project',
        	'description' => 'The Dandelion Project provides UniSA students on the autism spectrum with work experience at DXC Technology.',
        	'duration' => '157',
        	'thumbnail_url' => 'https://img.youtube.com/vi/2Q5gw5bWYWY/1.jpg',
        	'media_type' => 'youtube',
        	'added_by' => 2,
    		'created_at' => date("Y-m-d H:i:s")
        ]);



/*        DB::table('videos')->insert([
			'identifier' => '',
        	'title' => '',
        	'description' => '',
        	'duration' => '',
        	'thumbnail_url' => 'https://img.youtube.com/vi/videoID/1.jpg',
        	'media_type' => 'youtube',
    		'created_at' => date("Y-m-d H:i:s")
        ]);        */

    }
}
