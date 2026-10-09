<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { ambilPeminjaman, tambahPeminjaman, ubahPeminjaman, uraikanError } from '../lib/api'
import { tanggalHariIni } from '../lib/peminjaman'

// id hanya ada pada rute /peminjaman/:id/ubah; tanpa id berarti mode tambah.
const props = defineProps({
  id: { type: String, default: null },
})

const router = useRouter()
const modeUbah = computed(() => Boolean(props.id))

const form = reactive({
  nama_peminjam: '',
  judul_buku: '',
  tanggal_pinjam: tanggalHariIni(),
  tanggal_kembali: '',
  status: 'dipinjam',
})
const errors = ref({})
const galat = ref('')
const memuat = ref(false)
const menyimpan = ref(false)

onMounted(async () => {
  if (!modeUbah.value) {
    return
  }

  memuat.value = true

  try {
    const data = await ambilPeminjaman(props.id)
    Object.assign(form, { ...data, tanggal_kembali: data.tanggal_kembali ?? '' })
  } catch (error) {
    galat.value = uraikanError(error).pesan
  } finally {
    memuat.value = false
  }
})

async function simpan() {
  menyimpan.value = true
  errors.value = {}
  galat.value = ''

  const isian = {
    nama_peminjam: form.nama_peminjam,
    judul_buku: form.judul_buku,
    tanggal_pinjam: form.tanggal_pinjam,
    tanggal_kembali: form.tanggal_kembali || null,
    status: form.status,
  }

  try {
    if (modeUbah.value) {
      await ubahPeminjaman(props.id, isian)
    } else {
      await tambahPeminjaman(isian)
    }

    router.push({ name: 'peminjaman', query: { aksi: modeUbah.value ? 'diubah' : 'ditambah' } })
  } catch (error) {
    const uraian = uraikanError(error)
    galat.value = uraian.pesan
    errors.value = uraian.errors
  } finally {
    menyimpan.value = false
  }
}
</script>

<template>
  <section class="card card-sempit">
    <h1>{{ modeUbah ? 'Ubah Peminjaman' : 'Tambah Peminjaman' }}</h1>

    <p v-if="galat" class="galat" role="alert">{{ galat }}</p>
    <p v-if="memuat">Memuat data...</p>

    <form v-else class="form" novalidate @submit.prevent="simpan">
      <label>
        Nama peminjam
        <input v-model.trim="form.nama_peminjam" type="text" name="nama_peminjam" maxlength="100" />
        <small v-if="errors.nama_peminjam" class="error-field">{{ errors.nama_peminjam[0] }}</small>
      </label>

      <label>
        Judul buku
        <input v-model.trim="form.judul_buku" type="text" name="judul_buku" maxlength="150" />
        <small v-if="errors.judul_buku" class="error-field">{{ errors.judul_buku[0] }}</small>
      </label>

      <div class="form-baris">
        <label>
          Tanggal pinjam
          <input v-model="form.tanggal_pinjam" type="date" name="tanggal_pinjam" />
          <small v-if="errors.tanggal_pinjam" class="error-field">
            {{ errors.tanggal_pinjam[0] }}
          </small>
        </label>

        <label>
          Tanggal kembali
          <input v-model="form.tanggal_kembali" type="date" name="tanggal_kembali" />
          <small v-if="errors.tanggal_kembali" class="error-field">
            {{ errors.tanggal_kembali[0] }}
          </small>
        </label>
      </div>

      <label>
        Status
        <select v-model="form.status" name="status">
          <option value="dipinjam">Dipinjam</option>
          <option value="dikembalikan">Dikembalikan</option>
        </select>
        <small v-if="errors.status" class="error-field">{{ errors.status[0] }}</small>
      </label>

      <div class="aksi">
        <button type="submit" class="btn" :disabled="menyimpan">
          {{ menyimpan ? 'Menyimpan...' : 'Simpan' }}
        </button>
        <RouterLink class="btn btn-sekunder" to="/peminjaman">Batal</RouterLink>
      </div>
    </form>
  </section>
</template>
