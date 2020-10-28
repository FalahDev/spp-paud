<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insertOrIgnore([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('arroudloh'),
            'role' => 'Admin',
            'created_at' => now()
        ]);
    }
}
