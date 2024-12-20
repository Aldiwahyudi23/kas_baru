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
            [
                'anggaran_id' => '3',
                'label_anggaran' => 'Max Pinjaman ke 2 (Minggu)',
                'catatan_anggaran' => '6',

            ],
            [
                'anggaran_id' => '3',
                'label_anggaran' => 'Lunas kurang sebulan (Minggu)',
                'catatan_anggaran' => '1',

            ],
            [
                'anggaran_id' => '3',
                'label_anggaran' => 'Pembayaran tanpa lebih (hari)',
                'catatan_anggaran' => '30',

            ],
            [
                'anggaran_id' => '3',
                'label_anggaran' => 'Uang Kasih Sayang',
                'catatan_anggaran' => '30000',

            ],
            [
                'anggaran_id' => '3',
                'label_anggaran' => 'Batas Setelah Pinjaman 2 (Minggu)',
                'catatan_anggaran' => '2',

            ],
            [
                'anggaran_id' => '3',
                'label_anggaran' => 'Batas Normal (Hari)',
                'catatan_anggaran' => '6',

            ],
        ]);
    }
}
