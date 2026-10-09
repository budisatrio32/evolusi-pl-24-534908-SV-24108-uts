@extends('layouts.app')

@section('title', 'Tambah Peminjaman')

@section('content')
    <div class="card">
        <h1>Tambah Peminjaman</h1>
        <form method="POST" action="{{ route('peminjaman.store') }}" class="form">
            @include('peminjaman._form')
        </form>
    </div>
@endsection
