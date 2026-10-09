<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_halaman_tentang_dapat_diakses(): void
    {
        $this->get('/tentang')->assertOk();
    }

    public function test_halaman_tentang_menampilkan_identitas_penyusun(): void
    {
        $this->get('/tentang')->assertSee('24/534908/SV/24108');
    }

    public function test_navigasi_menampilkan_tautan_ke_halaman_tentang(): void
    {
        $this->get('/')->assertSee(route('about'));
    }
}
