<?php

use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');
Route::get('/tentang', fn () => view('about'))->name('about');

Route::resource('peminjaman', PeminjamanController::class)
    ->except('show')
    ->parameters(['peminjaman' => 'peminjaman']);
