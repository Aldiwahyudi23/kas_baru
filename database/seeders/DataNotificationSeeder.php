<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('data_notifications')->insert([
            [
                'name' => 'Kas Payment',
                'type' => 'Pengajuan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Kas Payment',
                'type' => 'Konfirmasi',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pinjaman',
                'type' => 'Pengajuan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pinjaman',
                'type' => 'Konfirmasi',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pinjaman',
                'type' => 'Pencairan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pinjaman',
                'type' => 'Diterima',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Bayar Pinjaman',
                'type' => 'Pengajuan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Bayar Pinjaman',
                'type' => 'Konfirmasi',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pengeluaran',
                'type' => 'Pengajuan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pengeluaran',
                'type' => 'Konfirmasi',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '0',
                'anggota' => '0',
                'program' => '1',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pengeluaran',
                'type' => 'Pencairan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pemasukan',
                'type' => 'Pengajuan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pemasukan',
                'type' => 'Konfirmasi',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '0',
                'anggota' => '0',
                'program' => '1',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Konter',
                'type' => 'Pengajuan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Konter',
                'type' => 'Berhasil',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Setor Tunai',
                'type' => 'Pengajuan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Setor Tunai',
                'type' => 'Konfirmasi',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pinjaman ke 2',
                'type' => 'Pengajuan',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '1',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],
            [
                'name' => 'Pinjaman ke 2',
                'type' => 'Konfirmasi',
                'wa_notification' => '1',
                'email_notification' => '1',
                'pengurus' => '0',
                'anggota' => '1',
                'program' => '0',
                'keterangan' => 'keterangan',
            ],

            // Tambahkan lebih banyak admin sesuai kebutuhan
        ]);
    }
}