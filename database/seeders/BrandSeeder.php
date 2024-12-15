<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brands')->insert([
            'id' => 1,
            'name' => 'GERMAN BESSARAB',
        ]);
        DB::table('brands')->insert([
            'id' => 2,
            'name' => 'GET LOW',
        ]);
    }
}
