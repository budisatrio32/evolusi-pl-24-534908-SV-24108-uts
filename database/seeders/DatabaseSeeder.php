<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Tidak memakai Factory, jadi aman dijalankan di container produksi.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PeminjamanSeeder::class,
        ]);
    }
}
