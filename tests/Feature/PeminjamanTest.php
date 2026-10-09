<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanTest extends TestCase
{
    use RefreshDatabase;

    private function dataValid(array $ubah = []): array
    {
        return array_merge([
            'nama_peminjam' => 'Budi',
            'judul_buku' => 'Clean Code',
            'tanggal_pinjam' => '2026-09-01',
            'tanggal_kembali' => null,
            'status' => 'dipinjam',
        ], $ubah);
    }

    public function test_halaman_daftar_peminjaman_menampilkan_data(): void
    {
        $peminjaman = Peminjaman::factory()->create(['judul_buku' => 'Refactoring']);

        $this->get(route('peminjaman.index'))
            ->assertOk()
            ->assertSee($peminjaman->judul_buku);
    }

    public function test_halaman_tambah_peminjaman_dapat_diakses(): void
    {
        $this->get(route('peminjaman.create'))->assertOk();
    }

    public function test_peminjaman_baru_dapat_disimpan(): void
    {
        $this->post(route('peminjaman.store'), $this->dataValid())
            ->assertRedirect(route('peminjaman.index'));

        $this->assertDatabaseHas('peminjaman', ['nama_peminjam' => 'Budi', 'judul_buku' => 'Clean Code']);
    }

    public function test_peminjaman_tanpa_judul_buku_ditolak(): void
    {
        $this->post(route('peminjaman.store'), $this->dataValid(['judul_buku' => '']))
            ->assertSessionHasErrors('judul_buku');

        $this->assertDatabaseCount('peminjaman', 0);
    }

    public function test_tanggal_kembali_tidak_boleh_sebelum_tanggal_pinjam(): void
    {
        $this->post(route('peminjaman.store'), $this->dataValid(['tanggal_kembali' => '2026-08-01']))
            ->assertSessionHasErrors('tanggal_kembali');
    }

    public function test_peminjaman_dapat_diubah_menjadi_dikembalikan(): void
    {
        $peminjaman = Peminjaman::factory()->create();

        $this->get(route('peminjaman.edit', $peminjaman))->assertOk();

        $this->put(route('peminjaman.update', $peminjaman), $this->dataValid([
            'tanggal_kembali' => '2026-09-10',
            'status' => 'dikembalikan',
        ]))->assertRedirect(route('peminjaman.index'));

        $this->assertSame('dikembalikan', $peminjaman->fresh()->status);
    }

    public function test_peminjaman_dapat_dihapus(): void
    {
        $peminjaman = Peminjaman::factory()->create();

        $this->delete(route('peminjaman.destroy', $peminjaman))
            ->assertRedirect(route('peminjaman.index'));

        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
    }
}
