import axios from 'axios'
import { hapusSesi, sesi } from './auth'

/**
 * Alamat dasar API dibaca dari VITE_API_URL, bukan ditulis langsung di kode.
 * Nilai VITE_ ikut tertanam di hasil build dan bisa dibaca siapa pun yang membuka
 * aplikasi, jadi hanya boleh berisi hal publik seperti alamat API, bukan rahasia.
 */
export const apiBaseUrl = import.meta.env.VITE_API_URL || '/api'

/** Satu instance Axios untuk seluruh aplikasi. */
export const http = axios.create({
  baseURL: apiBaseUrl,
  timeout: 10000,
  headers: { Accept: 'application/json' },
})

/**
 * Request interceptor: menyisipkan header Authorization: Bearer <token>
 * ke setiap permintaan selama user sudah login.
 */
export function sisipkanToken(config) {
  if (sesi.token) {
    config.headers.Authorization = `Bearer ${sesi.token}`
  }

  return config
}

let saatSesiHabis = () => {}

/** Dipasang dari main.js supaya http.js tidak perlu mengimpor router. */
export function aturPenangananSesiHabis(fn) {
  saatSesiHabis = fn
}

/**
 * Response interceptor: bila server membalas 401 padahal user merasa sudah login
 * (token dicabut atau kedaluwarsa), sesi lokal dihapus lalu user diarahkan ke login.
 */
export function tanganiErrorRespons(error) {
  if (error.response?.status === 401 && sesi.token) {
    hapusSesi()
    saatSesiHabis()
  }

  return Promise.reject(error)
}

http.interceptors.request.use(sisipkanToken)
http.interceptors.response.use((response) => response, tanganiErrorRespons)
