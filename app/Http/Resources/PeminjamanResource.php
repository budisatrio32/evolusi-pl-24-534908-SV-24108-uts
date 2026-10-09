<?php

namespace App\Http\Resources;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Bentuk JSON satu data peminjaman. Semua endpoint memakai bentuk yang sama,
 * dan tanggal dikirim sebagai YYYY-MM-DD supaya mudah dipakai di input date Vue.
 *
 * @mixin Peminjaman
 */
class PeminjamanResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_peminjam' => $this->nama_peminjam,
            'judul_buku' => $this->judul_buku,
            'tanggal_pinjam' => $this->tanggal_pinjam->toDateString(),
            'tanggal_kembali' => $this->tanggal_kembali?->toDateString(),
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
