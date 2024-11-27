<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanyInformationSeeder::class,
            ProgramSeeder::class,
            AnggaranSeeder::class,
            ProgramSettSeeder::class,
            AnggaranSettSeeder::class,
            RoleSeeder::class,
            LayoutsFormSeeder::class,
            // AccessNotificationSeeder::class,
            // Tambahkan seeder lain yang Anda miliki
        ]);
        // User::factory(10)->create();
        Admin::factory()->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


    }
}
