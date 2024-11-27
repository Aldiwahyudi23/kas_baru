<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnggaranSettSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('anggaran_settings')->insert([
            [
                'anggaran_id' => '1',
                'label_anggaran' => 'persentase',
                'catatan_anggaran' => '22.5',

            ],
            [
                'anggaran_id' => '2',
                'label_anggaran' => 'persentase',
                'catatan_anggaran' => '5',

            ],
            [
                'anggaran_id' => '3',
                'label_anggaran' => 'persentase',
                'catatan_anggaran' => '22.5',

            ],
            [
                'anggaran_id' => '4',
                'label_anggaran' => 'persentase',
                'catatan_anggaran' => '50',

            ],
            [
                'anggaran_id' => '3',
                'label_anggaran' => 'Alokasi Anggaran Max',
                'catatan_anggaran' => '500000',

            ],
            [
                'anggaran_id' => '3',
                'label_anggaran' => 'Max Pinjaman (Bulan)',
                'catatan_anggaran' => '3',

            ],
        ]);
    }
}