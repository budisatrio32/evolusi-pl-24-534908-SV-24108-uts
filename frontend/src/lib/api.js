import { http } from './http'

/**
 * Semua panggilan ke API Laravel lewat Axios. Komponen Vue cukup memanggil
 * fungsi-fungsi di sini tanpa perlu tahu alamat endpoint maupun header token.
 */

export async function login(email, password) {
  const { data } = await http.post('/login', { email, password })

  return data
}

export async function logout() {
  await http.post('/logout')
}

export async function daftarPeminjaman() {
  const { data } = await http.get('/peminjaman')

  return data.data ?? []
}

export async function ambilPeminjaman(id) {
  const { data } = await http.get(`/peminjaman/${id}`)

  return data.data
}

export async function tambahPeminjaman(isian) {
  const { data } = await http.post('/peminjaman', isian)

  return data.data
}

export async function ubahPeminjaman(id, isian) {
  const { data } = await http.put(`/peminjaman/${id}`, isian)

  return data.data
}

export async function hapusPeminjaman(id) {
  await http.delete(`/peminjaman/${id}`)
}

/**
 * Mengubah error Axios menjadi pesan yang siap ditampilkan.
 * Untuk 422, `errors` berisi pesan per field dari validasi Laravel.
 */
export function uraikanError(error) {
  if (!error?.response) {
    return {
      pesan: 'Server tidak dapat dihubungi. Periksa backend dan VITE_API_URL.',
      errors: {},
    }
  }

  const { status, data } = error.response

  if (status === 422) {
    return { pesan: 'Periksa kembali isian formulir.', errors: data?.errors ?? {} }
  }

  return { pesan: data?.message ?? `Terjadi kesalahan (HTTP ${status}).`, errors: {} }
}
