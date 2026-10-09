<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Entrypoint container memanggil `php artisan db:seed` setiap kali start,
 * jadi seeder wajib aman dijalankan berulang kali.
 */
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_membuat_akun_demo_dan_data_contoh(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', ['email' => 'admin@kepl.test']);
        $this->assertSame(3, Peminjaman::count());
    }

    public function test_seeder_dijalankan_berulang_tidak_menggandakan_data(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(1, User::where('email', 'admin@kepl.test')->count());
        $this->assertSame(3, Peminjaman::count());
    }

    public function test_seeder_tidak_menimpa_data_yang_sudah_ada(): void
    {
        Peminjaman::factory()->create(['judul_buku' => 'Buku Milik User']);

        $this->seed();

        $this->assertSame(1, Peminjaman::count());
        $this->assertDatabaseHas('peminjaman', ['judul_buku' => 'Buku Milik User']);
    }
}
