<?php

namespace App\Models;

use Database\Factories\PeminjamanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    /** @use HasFactory<PeminjamanFactory> */
    use HasFactory;

    public const STATUS = ['dipinjam', 'dikembalikan'];

    protected $table = 'peminjaman';

    protected $fillable = [
        'nama_peminjam',
        'judul_buku',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_kembali' => 'date',
        ];
    }
}
