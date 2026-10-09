<?php

namespace Tests\Feature\Api;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PeminjamanApiTest extends TestCase
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

    private function login(): void
    {
        Sanctum::actingAs(User::factory()->create());
    }

    // ---------------------------------------------------------------- 401

    public function test_semua_endpoint_peminjaman_wajib_token(): void
    {
        $peminjaman = Peminjaman::factory()->create();

        $this->getJson('/api/peminjaman')->assertUnauthorized();
        $this->postJson('/api/peminjaman', $this->dataValid())->assertUnauthorized();
        $this->getJson("/api/peminjaman/{$peminjaman->id}")->assertUnauthorized();
        $this->putJson("/api/peminjaman/{$peminjaman->id}", $this->dataValid())->assertUnauthorized();
        $this->deleteJson("/api/peminjaman/{$peminjaman->id}")->assertUnauthorized();

        $this->assertDatabaseCount('peminjaman', 1);
    }

    public function test_token_palsu_ditolak_401(): void
    {
        $this->withToken('1|token-palsu')
            ->getJson('/api/peminjaman')
            ->assertUnauthorized();
    }

    // ---------------------------------------------------------------- index

    public function test_daftar_peminjaman_mengembalikan_json(): void
    {
        $this->login();
        Peminjaman::factory()->create($this->dataValid());

        $this->getJson('/api/peminjaman')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'nama_peminjam' => 'Budi',
                'judul_buku' => 'Clean Code',
                'tanggal_pinjam' => '2026-09-01',
                'tanggal_kembali' => null,
                'status' => 'dipinjam',
            ]);
    }

    public function test_daftar_peminjaman_kosong(): void
    {
        $this->login();

        $this->getJson('/api/peminjaman')
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    // ---------------------------------------------------------------- store

    public function test_peminjaman_baru_disimpan_dengan_status_201(): void
    {
        $this->login();

        $this->postJson('/api/peminjaman', $this->dataValid())
            ->assertCreated()
            ->assertJsonPath('message', 'Data peminjaman berhasil ditambahkan.')
            ->assertJsonPath('data.judul_buku', 'Clean Code')
            ->assertJsonStructure(['data' => ['id', 'nama_peminjam', 'judul_buku', 'tanggal_pinjam', 'tanggal_kembali', 'status']]);

        $this->assertDatabaseHas('peminjaman', ['nama_peminjam' => 'Budi', 'judul_buku' => 'Clean Code']);
    }

    public function test_peminjaman_tanpa_isian_wajib_ditolak_422(): void
    {
        $this->login();

        $this->postJson('/api/peminjaman', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama_peminjam', 'judul_buku', 'tanggal_pinjam', 'status'])
            ->assertJsonPath('errors.judul_buku.0', 'Judul buku wajib diisi.');

        $this->assertDatabaseCount('peminjaman', 0);
    }

    public function test_tanggal_kembali_sebelum_tanggal_pinjam_ditolak_422(): void
    {
        $this->login();

        $this->postJson('/api/peminjaman', $this->dataValid(['tanggal_kembali' => '2026-08-01']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('tanggal_kembali');
    }

    public function test_status_di_luar_pilihan_ditolak_422(): void
    {
        $this->login();

        $this->postJson('/api/peminjaman', $this->dataValid(['status' => 'hilang']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    // ---------------------------------------------------------------- show

    public function test_detail_peminjaman_200(): void
    {
        $this->login();
        $peminjaman = Peminjaman::factory()->create(['judul_buku' => 'Refactoring']);

        $this->getJson("/api/peminjaman/{$peminjaman->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $peminjaman->id)
            ->assertJsonPath('data.judul_buku', 'Refactoring');
    }

    public function test_detail_peminjaman_yang_tidak_ada_404(): void
    {
        $this->login();

        $this->getJson('/api/peminjaman/999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Data tidak ditemukan.']);
    }

    // ---------------------------------------------------------------- update

    public function test_peminjaman_dapat_diubah_menjadi_dikembalikan(): void
    {
        $this->login();
        $peminjaman = Peminjaman::factory()->create($this->dataValid());

        $this->putJson("/api/peminjaman/{$peminjaman->id}", $this->dataValid([
            'tanggal_kembali' => '2026-09-10',
            'status' => 'dikembalikan',
        ]))
            ->assertOk()
            ->assertJsonPath('data.status', 'dikembalikan')
            ->assertJsonPath('data.tanggal_kembali', '2026-09-10');

        $this->assertSame('dikembalikan', $peminjaman->fresh()->status);
    }

    public function test_ubah_peminjaman_yang_tidak_ada_404(): void
    {
        $this->login();

        $this->putJson('/api/peminjaman/999', $this->dataValid())->assertNotFound();
    }

    public function test_ubah_peminjaman_dengan_data_tidak_valid_422(): void
    {
        $this->login();
        $peminjaman = Peminjaman::factory()->create($this->dataValid());

        $this->putJson("/api/peminjaman/{$peminjaman->id}", $this->dataValid(['nama_peminjam' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('nama_peminjam');

        $this->assertSame('Budi', $peminjaman->fresh()->nama_peminjam);
    }

    // ---------------------------------------------------------------- destroy

    public function test_peminjaman_dapat_dihapus(): void
    {
        $this->login();
        $peminjaman = Peminjaman::factory()->create();

        $this->deleteJson("/api/peminjaman/{$peminjaman->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Data peminjaman berhasil dihapus.');

        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
    }

    public function test_hapus_peminjaman_yang_tidak_ada_404(): void
    {
        $this->login();

        $this->deleteJson('/api/peminjaman/999')->assertNotFound();
    }
}
