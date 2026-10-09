<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_halaman_beranda_dapat_diakses(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_halaman_beranda_menampilkan_judul_aplikasi(): void
    {
        $this->get('/')->assertSee('Aplikasi Web Sederhana');
    }
}
