<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderKonterSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('provider_konters')->insert([
            [
                'kategori_id' => '2',
                'name' => 'Tagihan Listrik',
                'description' => 'description',
            ],
            // Tambahkan lebih banyak admin sesuai kebutuhan
        ]);
    }
}
