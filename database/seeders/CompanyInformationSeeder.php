<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_information')->insert([
            [
                'code' => 'company',
                'company_name' => 'company_name',
                'description' => 'description',
                'vision' => 'vision',
                'mission' => 'mission',
                'logo' => 'logo', // Validasi untuk logo
                'address' => 'address',
                'phone_number' => 'phone_number',
                'email' => 'email',
            ],
            // Tambahkan lebih banyak admin sesuai kebutuhan
        ]);
    }
}
