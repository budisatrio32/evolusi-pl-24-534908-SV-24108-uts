<script setup>
import { apiBaseUrl } from '../lib/http'
import { sesi } from '../lib/auth'
</script>

<template>
  <section class="card">
    <h1>Peminjaman Buku</h1>
    <p>
      Aplikasi Vue 3 ini adalah antarmuka untuk RESTful API Laravel pada repository
      <code>evolusi-pl-24-534908-SV-24108-uts</code>. Data diambil lewat Axios dengan
      token Laravel Sanctum.
    </p>

    <dl>
      <dt>Nama</dt>
      <dd>Prihastomo Budi Satrio</dd>
      <dt>NIM</dt>
      <dd>24/534908/SV/24108</dd>
      <dt>Alamat API</dt>
      <dd><code>{{ apiBaseUrl }}</code></dd>
    </dl>

    <RouterLink v-if="sesi.token" class="btn" to="/peminjaman">Kelola data peminjaman</RouterLink>
    <RouterLink v-else class="btn" to="/login">Masuk untuk mengelola data</RouterLink>
  </section>
</template>
