<?php

namespace Database\Factories;

use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_peminjam' => fake()->name(),
            'judul_buku' => fake()->sentence(3),
            'tanggal_pinjam' => fake()->dateTimeBetween('-1 month')->format('Y-m-d'),
            'tanggal_kembali' => null,
            'status' => 'dipinjam',
        ];
    }
}
