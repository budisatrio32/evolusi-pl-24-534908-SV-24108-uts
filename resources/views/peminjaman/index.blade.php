@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('content')
    <div class="card">
        <div class="toolbar">
            <h1>Peminjaman Buku</h1>
            <a class="btn" href="{{ route('peminjaman.create') }}">+ Tambah</a>
        </div>

        @if (session('pesan'))
            <p class="alert">{{ session('pesan') }}</p>
        @endif

        @if ($peminjaman->isEmpty())
            <p class="muted">Belum ada data peminjaman.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peminjaman as $item)
                            <tr>
                                <td>{{ $item->nama_peminjam }}</td>
                                <td>{{ $item->judul_buku }}</td>
                                <td>{{ $item->tanggal_pinjam->format('d-m-Y') }}</td>
                                <td>{{ $item->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td>
                                <td><span class="badge {{ $item->status }}">{{ ucfirst($item->status) }}</span></td>
                                <td class="aksi">
                                    <a href="{{ route('peminjaman.edit', $item) }}">Ubah</a>
                                    <form method="POST" action="{{ route('peminjaman.destroy', $item) }}"
                                          onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="link-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
