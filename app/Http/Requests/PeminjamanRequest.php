<?php

namespace App\Http\Requests;

use App\Models\Peminjaman;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Dipakai bersama oleh POST (tambah) dan PUT (ubah) /api/peminjaman.
 */
class PeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama_peminjam' => ['required', 'string', 'max:100'],
            'judul_buku' => ['required', 'string', 'max:150'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
            'status' => ['required', Rule::in(Peminjaman::STATUS)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'status.in' => 'Status harus dipinjam atau dikembalikan.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_peminjam' => 'Nama peminjam',
            'judul_buku' => 'Judul buku',
            'tanggal_pinjam' => 'Tanggal pinjam',
            'tanggal_kembali' => 'Tanggal kembali',
            'status' => 'Status',
        ];
    }
}
