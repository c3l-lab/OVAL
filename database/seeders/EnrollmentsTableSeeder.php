<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EnrollmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $c1 = oval\Models\Course::find(10000000);
        // $c2 = oval\Models\Course::find(10000001);
        $c = \oval\Models\Course::first();

        // $minniemouse = oval\Models\User::where([
        //     ['first_name', 'Minnie'], ['last_name', 'Mouse']
        // ])->first();
        // $mickeymouse = oval\Models\User::where([
        //     ['first_name', 'Mickey'], ['last_name', 'Mouse']
        // ])->first();
        // $donald = oval\Models\User::where('first_name', 'Donald')->first();
        // $daisy = oval\Models\User::where('first_name', 'Daisy')->first();
        // $janesmith = oval\Models\User::where([
        //     ['first_name','Jane'], ['last_name', 'Smith']
        // ])->first();
        // $edward = oval\Models\User::where('first_name', 'Edward')->first();
        // $billy = oval\Models\User::where('first_name', 'Billy')->first();
        // $kerry = oval\Models\User::where('first_name', 'Kerry')->first();
        // $alex = oval\Models\User::where('first_name', 'Alex')->first();
        // $steve = oval\Models\User::where('first_name', 'Steve')->first();
        $johndoe = \oval\Models\User::where([
            ['first_name', 'John'], ['last_name', 'Doe']
        ])->first();
        $student = \oval\Models\User::where([
            ['first_name', 'Student'], ['last_name', 'Name']
        ])->first();

        \DB::table('enrollments')->insert([
            ['course_id'=>$c->id, 'user_id'=>$johndoe->id, 'is_instructor'=>true],
            ['course_id'=>$c->id, 'user_id'=>$student->id, 'is_instructor'=>false],
            // ['course_id'=>$c1->id, 'user_id'=>$janesmith->id, 'is_instructor'=>false],
            // ['course_id'=>$c2->id, 'user_id'=>$janesmith->id, 'is_instructor'=>false],
            // ['course_id'=>$c1->id, 'user_id'=>$minniemouse->id, 'is_instructor'=>true],
            // ['course_id'=>$c2->id, 'user_id'=>$minniemouse->id, 'is_instructor'=>true],
            // ['course_id'=>$c1->id, 'user_id'=>$mickeymouse->id, 'is_instructor'=>true],
            // ['course_id'=>$c2->id, 'user_id'=>$mickeymouse->id, 'is_instructor'=>true],
            // ['course_id'=>$c1->id, 'user_id'=>$donald->id, 'is_instructor'=>true],
            // ['course_id'=>$c2->id, 'user_id'=>$donald->id, 'is_instructor'=>true],
            // ['course_id'=>$c1->id, 'user_id'=>$daisy->id, 'is_instructor'=>true],
            // ['course_id'=>$c1->id, 'user_id'=>$edward->id, 'is_instructor'=>false],
            // ['course_id'=>$c2->id, 'user_id'=>$edward->id, 'is_instructor'=>false],
            // ['course_id'=>$c1->id, 'user_id'=>$billy->id, 'is_instructor'=>false],
            // ['course_id'=>$c2->id, 'user_id'=>$billy->id, 'is_instructor'=>false],
            // ['course_id'=>$c1->id, 'user_id'=>$kerry->id, 'is_instructor'=>false],
            // ['course_id'=>$c2->id, 'user_id'=>$kerry->id, 'is_instructor'=>false],
            // ['course_id'=>$c1->id, 'user_id'=>$alex->id, 'is_instructor'=>false],
            // ['course_id'=>$c2->id, 'user_id'=>$steve->id, 'is_instructor'=>false],
        ]);
        //       DB::table('enrollments')->insert([
        //         'course_id'=>1,
        //         'user_id'=>1,
        //         'is_instructor'=>true
        //       ]);
    }
}
