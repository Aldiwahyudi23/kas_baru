<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSettSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('program_settings')->insert([
            [
                'program_id' => '1',
                'label_program' => 'nominal',
                'catatan_program' => '50000',
            ],
            // Tambahkan lebih banyak admin sesuai kebutuhan
        ]);
    }
}