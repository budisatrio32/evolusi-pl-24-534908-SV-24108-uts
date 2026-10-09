import { reactive } from 'vue'

/**
 * Sesi login disimpan di localStorage supaya tetap ada setelah halaman di-refresh.
 * Objek `sesi` bersifat reactive, jadi navbar ikut berubah saat login/logout.
 */
const KUNCI_TOKEN = 'kepl_token'
const KUNCI_USER = 'kepl_user'

function baca(kunci) {
  try {
    return localStorage.getItem(kunci)
  } catch {
    return null
  }
}

function bacaUser() {
  try {
    return JSON.parse(baca(KUNCI_USER) ?? 'null')
  } catch {
    return null
  }
}

export const sesi = reactive({
  token: baca(KUNCI_TOKEN),
  user: bacaUser(),
})

export function simpanSesi(token, user) {
  sesi.token = token
  sesi.user = user

  try {
    localStorage.setItem(KUNCI_TOKEN, token)
    localStorage.setItem(KUNCI_USER, JSON.stringify(user))
  } catch {
    // Penyimpanan diblokir browser: sesi tetap berlaku sampai halaman ditutup.
  }
}

export function hapusSesi() {
  sesi.token = null
  sesi.user = null

  try {
    localStorage.removeItem(KUNCI_TOKEN)
    localStorage.removeItem(KUNCI_USER)
  } catch {
    // Abaikan, sesi di memori sudah dikosongkan.
  }
}

export function sudahLogin() {
  return Boolean(sesi.token)
}
