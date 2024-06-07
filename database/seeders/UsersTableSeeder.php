<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use oval\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['id' => 10000001],
            [
                'id' => 10000001,
                'first_name' => 'Admin',
                'last_name' => 'admin',
                'email' => 'admin@example.com',
                'role' => 'A',
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            [
                'first_name' => 'John',
                'last_name' => 'Doe'
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'lec@example.com',
                'role' => 'O',
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            [
                'first_name' => 'Student',
                'last_name' => 'Name'
            ],
            [
                'first_name' => 'Student',
                'last_name' => 'Name',
                'email' => 'stu@example.com',
                'role' => 'O',
                'password' => bcrypt('password'),
            ]
        );
    }
}
