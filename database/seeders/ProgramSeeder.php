<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('programs')->insert([
            [
                'code' => 'P-000',
                'name' => 'Kas Keluarga',
                'description' => 'description',
                'snk' => 'Syarat dan Ketentuan',
                'is_active' => '1',
            ],
            // Tambahkan lebih banyak admin sesuai kebutuhan
        ]);
    }
}