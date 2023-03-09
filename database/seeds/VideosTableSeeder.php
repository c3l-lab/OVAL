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
        	'identifier' => '58617517',
        	'title' => 'Wayne testing 27 Jul',
        	'description' => 'Test',
        	'duration' => 30,
        	'thumbnail_url' => 'https://helix.example.com/thumbnails/58617517.jpg',
        	'media_type' => 'helix',
        	'added_by' => 2,
        	'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('videos')->insert([
        	'identifier' => '51245241',
        	'title' => 'Blooms Applying Verbs',
        	'description' => 'Narrated animation Blooms Applying Verbs',
        	'duration' => 36,
        	'thumbnail_url' => 'https://helix.example.com/thumbnails/51245241.jpg',
        	'media_type' => 'helix',
        	'added_by' => 2,
        	'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('videos')->insert([
        	'identifier' => '30336809',
        	'title' => 'PubMed 1 : Navigating to Pubmed',
        	'description' => 'Navigating to pubmed from the student portal',
        	'duration' => 48,
        	'thumbnail_url' => 'https://helix.example.com/thumbnails/30336809.jpg',
        	'media_type' => 'helix',
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
			'identifier' => '16682974',
        	'title' => 'PubMed 5 : Evaluating Search Results and Full Text',
        	'description' => 'Evaluating search results found in Pubmed, locating Full Text and exporting to citation manager or saving citations',
        	'duration' => '105',
        	'thumbnail_url' => 'https://helix.example.com/thumbnails/16682974.jpg',
        	'media_type' => 'helix',
        	'added_by' => 2,
    		'created_at' => date("Y-m-d H:i:s")
        ]);
        DB::table('videos')->insert([
			'identifier' => '73247170',
        	'title' => 'PubMed 2 : Searching PubMed | Searching Pubmed',
        	'description' => 'Searching Pubmed',
        	'duration' => '115',
        	'thumbnail_url' => 'https://helix.example.com/thumbnails/73247170.jpg',
        	'media_type' => 'helix',
        	'added_by' => 2,
    		'created_at' => date("Y-m-d H:i:s")
        ]);
		DB::table('videos')->insert([
			'identifier' => '89277314',
        	'title' => 'PubMed 4 : Pubmed&#039;s Search History',
        	'description' => 'Search history feature of PubMed',
        	'duration' => '91',
        	'thumbnail_url' => 'https://helix.example.com/thumbnails/89277314.jpg',
        	'media_type' => 'helix',
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
