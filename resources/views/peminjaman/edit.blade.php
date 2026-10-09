@extends('layouts.app')

@section('title', 'Ubah Peminjaman')

@section('content')
    <div class="card">
        <h1>Ubah Peminjaman</h1>
        <form method="POST" action="{{ route('peminjaman.update', $peminjaman) }}" class="form">
            @method('PUT')
            @include('peminjaman._form')
        </form>
    </div>
@endsection
