<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'code' => 'R-0001',
                'name' => 'Ketua',
                'description' => 'desktipsi',
                'is_active' => '1',

            ],
            [
                'code' => 'R-0002',
                'name' => 'Sekretaris',
                'description' => 'desktipsi',
                'is_active' => '1',

            ],
            [
                'code' => 'R-0003',
                'name' => 'Bendahara',
                'description' => 'desktipsi',
                'is_active' => '1',

            ],
        ]);
    }
}