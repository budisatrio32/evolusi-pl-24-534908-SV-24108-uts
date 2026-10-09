@extends('layouts.app')

@section('title', 'Tentang')

@section('content')
    <div class="card">
        <h1>Tentang Aplikasi</h1>
        <p>
            Aplikasi ini merupakan tugas mata kuliah Konstruksi dan Evolusi Perangkat Lunak
            yang berfokus pada praktik pengelolaan kode sumber, bukan pada kompleksitas fitur.
        </p>

        <h2>Identitas</h2>
        <ul>
            <li>Nama: Prihastomo Budi Satrio</li>
            <li>NIM: 24/534908/SV/24108</li>
            <li>Kerangka kerja: Laravel {{ app()->version() }}</li>
        </ul>

        <h2>Praktik yang Diterapkan</h2>
        <ul>
            <li>Penamaan commit mengikuti standar Conventional Commits.</li>
            <li>Pengembangan bertingkat melalui branch <code>main</code>, <code>dev</code>, dan <code>feature/*</code>.</li>
            <li>Penggabungan perubahan hanya melalui Pull Request.</li>
            <li>Continuous Integration otomatis pada setiap push dan Pull Request.</li>
        </ul>
    </div>
@endsection
