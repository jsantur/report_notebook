<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FixAdminUserSeeder::class,
            SettingSeeder::class,
            ImportSerenazgosSeeder::class,
            CamaraSeeder::class,
            MegafonoSeeder::class,
        ]);
    }
}
