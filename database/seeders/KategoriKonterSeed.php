<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriKonterSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori_konters')->insert([
            [
                'name' => 'Pulsa',
                'description' => 'description',
            ],
            [
                'name' => 'Listrik',
                'description' => 'description',
            ],
            // Tambahkan lebih banyak admin sesuai kebutuhan
        ]);
    }
}
