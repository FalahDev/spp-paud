<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pengaturan')->insertOrIgnore([
            'key' => 'nama',
            'name' => 'Nama',
            'value' => 'Sistem SPP SDIT Ar Roudloh',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
