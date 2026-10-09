import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    // Saat `npm run dev`, permintaan ke /api diteruskan ke Laravel (php artisan serve).
    // Polanya sama dengan Nginx di container produksi, jadi VITE_API_URL cukup "/api"
    // dan browser tidak perlu CORS karena frontend dan API terlihat satu origin.
    proxy: {
      '/api': 'http://127.0.0.1:8000',
    },
  },
})
