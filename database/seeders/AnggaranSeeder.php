<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnggaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('anggarans')->insert([
            [
                'code' => 'AG-001',
                'program_id' => '1',
                'code_anggaran' => 'DD',
                'name' => 'Dana Darurat',
                'description' => 'description',
                'is_active' => '1',
            ],
            [
                'code' => 'AG-002',
                'program_id' => '1',
                'code_anggaran' => 'DA',
                'name' => 'Dana Amal',
                'description' => 'description',
                'is_active' => '1',
            ],
            [
                'code' => 'AG-003',
                'program_id' => '1',
                'code_anggaran' => 'DP',
                'name' => 'Dana Pinjam',
                'description' => 'description',
                'is_active' => '1',
            ],
            [
                'code' => 'AG-004',
                'program_id' => '1',
                'code_anggaran' => 'DK',
                'name' => 'Dana Kas',
                'description' => 'description',
                'is_active' => '1',
            ],
            [
                'code' => 'AG-005',
                'program_id' => '1',
                'code_anggaran' => 'DU',
                'name' => 'Dana Usaha',
                'description' => 'description',
                'is_active' => '0',
            ],
            [
                'code' => 'AG-006',
                'program_id' => '1',
                'code_anggaran' => 'DC',
                'name' => 'Dana Acara',
                'description' => 'description',
                'is_active' => '0',
            ],

        ]);
    }
}