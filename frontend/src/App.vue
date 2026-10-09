<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { logout } from './lib/api'
import { hapusSesi, sesi } from './lib/auth'

const router = useRouter()

async function keluar() {
  try {
    await logout() // cabut token di server
  } catch {
    // Token mungkin sudah tidak berlaku; sesi lokal tetap dihapus.
  }

  hapusSesi()
  router.push({ name: 'login' })
}
</script>

<template>
  <header>
    <nav>
      <span class="brand">Peminjaman Buku</span>
      <RouterLink to="/">Beranda</RouterLink>

      <template v-if="sesi.token">
        <RouterLink to="/peminjaman">Peminjaman</RouterLink>
        <span class="muted">{{ sesi.user?.name }}</span>
        <button type="button" class="btn btn-link" @click="keluar">Keluar</button>
      </template>
      <RouterLink v-else to="/login">Masuk</RouterLink>
    </nav>
  </header>

  <main>
    <RouterView />
  </main>

  <footer>Konstruksi dan Evolusi Perangkat Lunak &middot; UTS &middot; Vue 3 + Laravel 12</footer>
</template>
