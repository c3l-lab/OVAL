<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 10000000,
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@a.com',
            'role' => 'A',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'id' => 10000001,
            'first_name' => 'Minnie',
            'last_name' => 'Mouse',
            'email' => 'minnie@a.com',
            'role' => 'O',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'id' => 10000002,
            'first_name' => 'Mickey',
            'last_name' => 'Mouse',
            'email' => 'mickey@a.com',
            'role' => 'O',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'id' => 10000003,
            'first_name' => 'Donald',
            'last_name' => 'Duck',
            'email' => 'donald@a.com',
            'role' => 'O',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'id' => 10000004,
            'first_name' => 'Daisy',
            'last_name' => 'Duck',
            'email' => 'daisy@a.com',
            'role' => 'O',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'id' => 10000005,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'js@a.com',
            'role' => 'O',
            'password' => bcrypt('password')
        ]);
        DB::table('users')->insert([
            'id' => 10000006,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'jd@a.com',
            'role' => 'O',
            'password' => bcrypt('password')
        ]);
        DB::table('users')->insert([
            'id' => 10000007,
            'first_name' => 'Edward',
            'last_name' => 'David',
            'email' => 'ed@a.com',
            'role' => 'O',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'id' => 10000008,
            'first_name' => 'Billy',
            'last_name' => 'Moore',
            'email' => 'bm@a.com',
            'role' => 'O',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'id' => 10000009,
            'first_name' => 'Kerry',
            'last_name' => 'Sutton',
            'email' => 'ks@a.com',
            'role' => 'O',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'id' => 10000010,
            'first_name' => 'Alex',
            'last_name' => 'Wilson',
            'email' => 'aw@a.com',
            'role' => 'O',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'id' => 10000011,
            'first_name' => 'Steve',
            'last_name' => 'Cole',
            'email' => 'sc@a.com',
            'role' => 'O',
            'password' => bcrypt('password'),
        ]);
    }
}
