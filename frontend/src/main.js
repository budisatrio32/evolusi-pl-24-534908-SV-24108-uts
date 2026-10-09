import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { aturPenangananSesiHabis } from './lib/http'

// Token ditolak server (401) → kembali ke halaman login dengan keterangan sesi habis.
aturPenangananSesiHabis(() => {
  router.push({ name: 'login', query: { sesi: 'habis' } })
})

const app = createApp(App)

app.use(router)

app.mount('#app')