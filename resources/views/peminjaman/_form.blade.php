@if ($errors->any())
    <ul class="errors">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

@csrf

<label>Nama Peminjam
    <input type="text" name="nama_peminjam" value="{{ old('nama_peminjam', $peminjaman->nama_peminjam ?? '') }}" required>
</label>

<label>Judul Buku
    <input type="text" name="judul_buku" value="{{ old('judul_buku', $peminjaman->judul_buku ?? '') }}" required>
</label>

<label>Tanggal Pinjam
    <input type="date" name="tanggal_pinjam"
           value="{{ old('tanggal_pinjam', isset($peminjaman) ? $peminjaman->tanggal_pinjam->format('Y-m-d') : date('Y-m-d')) }}" required>
</label>

<label>Tanggal Kembali
    <input type="date" name="tanggal_kembali"
           value="{{ old('tanggal_kembali', isset($peminjaman) ? $peminjaman->tanggal_kembali?->format('Y-m-d') : '') }}">
</label>

<label>Status
    <select name="status">
        @foreach (\App\Models\Peminjaman::STATUS as $status)
            <option value="{{ $status }}" @selected(old('status', $peminjaman->status ?? 'dipinjam') === $status)>
                {{ ucfirst($status) }}
            </option>
        @endforeach
    </select>
</label>

<div class="toolbar">
    <a href="{{ route('peminjaman.index') }}">Batal</a>
    <button type="submit" class="btn">Simpan</button>
</div>
