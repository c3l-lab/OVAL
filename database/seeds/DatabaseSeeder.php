<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CoursesTableSeeder::class);
        $this->call(EnrollmentsTableSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(GroupMembersTableSeeder::class);
        $this->call(VideosTableSeeder::class);
        $this->call(GroupVideosTableSeeder::class);
        $this->call(AnnotationsTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
        $this->call(CommentInstructionsTableSeeder::class);
        $this->call(PointInstructionsTableSeeder::class);
        $this->call(PointsTableSeeder::class);
    }
}
