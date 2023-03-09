<?php

use Illuminate\Database\Seeder;

class GroupMembersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = oval\Group::all();
        
        $minnie = oval\User::where('first_name','Minnie')->first();
        $mickey = oval\User::where('first_name','Mickey')->first();
        $donald = oval\User::where('first_name','Donald')->first();
        $daisy = oval\User::where('first_name','Daisy')->first();
        $jane = oval\User::where('first_name', 'Jane')->first();
        $john = oval\User::where('first_name', 'John')->first();
        $edward = oval\User::where('first_name', 'Edward')->first();
		$billy = oval\User::where('first_name', 'Billy')->first();
		$kerry = oval\User::where('first_name', 'Kerry')->first();
		$alex = oval\User::where('first_name', 'Alex')->first();
		$steve = oval\User::where('first_name', 'Steve')->first();
        
        foreach ($groups as $g) {
            $g->addMember($minnie);
            $g->addMember($mickey);
            $g->addMember($donald);

        	if(preg_match('/1.*Default/', $g->name)) {
        		$g->addMember($jane);
        		$g->addMember($john);
                $g->addMember($edward);
                $g->addMember($billy);
                $g->addMember($kerry);
                $g->addMember($alex);
        	}
        	if(preg_match('/1.*Part/', $g->name)) {
        		$g->addMember($john);
                $g->addMember($edward);
        	}
            if(preg_match('/2.*Default/', $g->name)) {
                $g->addMember($daisy);
                $g->addMember($jane);
                $g->addMember($edward);
                $g->addMember($steve);
            }
        	if(preg_match('/2.*Part/', $g->name)) {
                $g->addMember($daisy);
        		$g->addMember($jane);
        	}
        	
        }

//         DB::table('group_members')->insert([
//         	'user_id' => 1,
//           'group_id' => 1,
//         ]);

    }
}
