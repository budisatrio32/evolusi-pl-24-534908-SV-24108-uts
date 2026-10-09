import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { sudahLogin } from '../lib/auth'

// Halaman lain dimuat terpisah (lazy load) supaya berkas awal tetap kecil.
const PeminjamanFormView = () => import('../views/PeminjamanFormView.vue')

export const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
    meta: { khususTamu: true },
  },
  {
    path: '/peminjaman',
    name: 'peminjaman',
    component: () => import('../views/PeminjamanView.vue'),
    meta: { perluLogin: true },
  },
  {
    path: '/peminjaman/tambah',
    name: 'peminjaman-tambah',
    component: PeminjamanFormView,
    meta: { perluLogin: true },
  },
  {
    path: '/peminjaman/:id/ubah',
    name: 'peminjaman-ubah',
    component: PeminjamanFormView,
    props: true,
    meta: { perluLogin: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

/**
 * Navigation guard: halaman ber-meta perluLogin hanya untuk yang punya token,
 * sedangkan halaman login tidak perlu dibuka lagi bila sudah login.
 */
export function penjagaRute(to, login = sudahLogin()) {
  if (to.meta?.perluLogin && !login) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta?.khususTamu && login) {
    return { name: 'peminjaman' }
  }

  return true
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach((to) => penjagaRute(to))

export default router
