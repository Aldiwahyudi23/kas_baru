<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayoutsFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('layouts_forms')->insert([
            [
                'icon_kas' => 'P-000',
                'icon_tabungan' => 'Kas Keluarga',
                'icon_b_pinjam' => 'description',
                'pinjam_pinjam' => 'Syarat dan Ketentuan',
                'kas_proses' => 'Pembayaran sedah di tinjau',
                'tabungan_proses' => 'Pembayaran sedah di tinjau',
                'b_pinjam_proses' => 'Pembayaran sedah di tinjau',
                'pinjam_proses' => 'Pembayaran sedah di tinjau',
                'pinjam_saldo' => 'Saldo belum cukup',
                'pinjam_penuh' => 'Pinjaman sudah Max',
                'pinjam_nunggak' => 'Masih ada pinjaman yang belum lunas',
            ],
            // Tambahkan lebih banyak admin sesuai kebutuhan
        ]);
    }
}
