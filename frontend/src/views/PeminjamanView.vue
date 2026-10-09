<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { daftarPeminjaman, hapusPeminjaman, uraikanError } from '../lib/api'
import { apiBaseUrl } from '../lib/http'
import { formatLabelPeminjaman, formatTanggal, labelStatus, ringkasStatus } from '../lib/peminjaman'

const PESAN_AKSI = {
  ditambah: 'Data peminjaman berhasil ditambahkan.',
  diubah: 'Data peminjaman berhasil diperbarui.',
}

const route = useRoute()

const daftar = ref([])
const galat = ref('')
const info = ref(PESAN_AKSI[route.query.aksi] ?? '')
const memuat = ref(true)

const ringkasan = computed(() => ringkasStatus(daftar.value))

async function muat() {
  memuat.value = true
  galat.value = ''

  try {
    daftar.value = await daftarPeminjaman()
  } catch (error) {
    galat.value = uraikanError(error).pesan
  } finally {
    memuat.value = false
  }
}

async function hapus(item) {
  if (!window.confirm(`Hapus peminjaman "${formatLabelPeminjaman(item)}"?`)) {
    return
  }

  try {
    await hapusPeminjaman(item.id)
    daftar.value = daftar.value.filter((data) => data.id !== item.id)
    info.value = 'Data peminjaman berhasil dihapus.'
  } catch (error) {
    galat.value = uraikanError(error).pesan
  }
}

onMounted(muat)
</script>

<template>
  <section class="card">
    <div class="judul-baris">
      <h1>Data Peminjaman</h1>
      <RouterLink class="btn" to="/peminjaman/tambah">+ Tambah</RouterLink>
    </div>
    <p class="muted">
      Sumber data: <code>GET {{ apiBaseUrl }}/peminjaman</code>
    </p>

    <p v-if="info" class="sukses" role="status">{{ info }}</p>
    <p v-if="galat" class="galat" role="alert">{{ galat }}</p>

    <p v-if="memuat">Memuat data...</p>

    <template v-else-if="!galat">
      <p class="ringkasan">
        Total <strong>{{ ringkasan.total }}</strong> peminjaman &middot; dipinjam
        <strong>{{ ringkasan.dipinjam }}</strong> &middot; dikembalikan
        <strong>{{ ringkasan.dikembalikan }}</strong>
      </p>

      <p v-if="!daftar.length" class="muted">Belum ada data peminjaman.</p>

      <ul v-else class="daftar">
        <li v-for="item in daftar" :key="item.id">
          <span class="judul">{{ formatLabelPeminjaman(item) }}</span>
          <span class="meta">
            {{ formatTanggal(item.tanggal_pinjam) }} &rarr;
            {{ formatTanggal(item.tanggal_kembali) }}
          </span>
          <span class="badge" :class="item.status">{{ labelStatus(item.status) }}</span>
          <span class="aksi">
            <RouterLink class="btn btn-kecil btn-sekunder" :to="`/peminjaman/${item.id}/ubah`">
              Ubah
            </RouterLink>
            <button type="button" class="btn btn-kecil btn-bahaya" @click="hapus(item)">
              Hapus
            </button>
          </span>
        </li>
      </ul>
    </template>
  </section>
</template>
