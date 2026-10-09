<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun demo untuk login dari Vue dan Postman.
     * Tanpa Factory/Faker supaya tetap jalan di image produksi (--no-dev).
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kepl.test'],
            ['name' => 'Admin Perpustakaan', 'password' => 'password'],
        );
    }
}
