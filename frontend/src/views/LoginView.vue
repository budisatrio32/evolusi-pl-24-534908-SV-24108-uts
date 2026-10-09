<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { login, uraikanError } from '../lib/api'
import { simpanSesi } from '../lib/auth'

const route = useRoute()
const router = useRouter()

const form = reactive({ email: '', password: '' })
const errors = ref({})
const pesan = ref(route.query.sesi === 'habis' ? 'Sesi berakhir, silakan masuk kembali.' : '')
const memproses = ref(false)

// Hanya alamat internal (diawali satu "/") yang boleh dipakai sebagai tujuan redirect.
function tujuanSetelahLogin() {
  const redirect = route.query.redirect

  return typeof redirect === 'string' && redirect.startsWith('/') && !redirect.startsWith('//')
    ? redirect
    : '/peminjaman'
}

async function kirim() {
  memproses.value = true
  errors.value = {}
  pesan.value = ''

  try {
    const hasil = await login(form.email, form.password)
    simpanSesi(hasil.access_token, hasil.user)
    router.replace(tujuanSetelahLogin())
  } catch (error) {
    const uraian = uraikanError(error)
    pesan.value = uraian.pesan
    errors.value = uraian.errors
  } finally {
    memproses.value = false
  }
}
</script>

<template>
  <section class="card card-sempit">
    <h1>Masuk</h1>
    <p class="muted">Gunakan akun demo <code>admin@kepl.test</code> / <code>password</code>.</p>

    <p v-if="pesan" class="galat" role="alert">{{ pesan }}</p>

    <form class="form" novalidate @submit.prevent="kirim">
      <label>
        Email
        <input v-model.trim="form.email" type="email" name="email" autocomplete="username" />
        <small v-if="errors.email" class="error-field">{{ errors.email[0] }}</small>
      </label>

      <label>
        Password
        <input
          v-model="form.password"
          type="password"
          name="password"
          autocomplete="current-password"
        />
        <small v-if="errors.password" class="error-field">{{ errors.password[0] }}</small>
      </label>

      <button type="submit" class="btn" :disabled="memproses">
        {{ memproses ? 'Memproses...' : 'Masuk' }}
      </button>
    </form>
  </section>
</template>
