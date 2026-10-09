<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::latest()->get();

        return view('peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        return view('peminjaman.create');
    }

    public function store(Request $request)
    {
        Peminjaman::create($this->validasi($request));

        return redirect()->route('peminjaman.index')
            ->with('pesan', 'Data peminjaman berhasil ditambahkan.');
    }

    public function edit(Peminjaman $peminjaman)
    {
        return view('peminjaman.edit', compact('peminjaman'));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $peminjaman->update($this->validasi($request));

        return redirect()->route('peminjaman.index')
            ->with('pesan', 'Data peminjaman berhasil diperbarui.');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        $peminjaman->delete();

        return redirect()->route('peminjaman.index')
            ->with('pesan', 'Data peminjaman berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_peminjam' => ['required', 'string', 'max:100'],
            'judul_buku' => ['required', 'string', 'max:150'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
            'status' => ['required', Rule::in(Peminjaman::STATUS)],
        ]);
    }
}
