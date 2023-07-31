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
        User::factory()->create([
            'id' => 10000001,
            'first_name' => 'Admin',
            'last_name' => 'admin',
            'email' => 'admin@example.com',
            'role' => 'A',
            'password' => bcrypt('password'),
        ]);
    }
}
