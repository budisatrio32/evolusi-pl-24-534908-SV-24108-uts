# evolusi-pl-24-534908-SV-24108-uts

Proyek Ujian Tengah Semester mata kuliah **Konstruksi dan Evolusi Perangkat Lunak**:
aplikasi **Peminjaman Buku** dengan backend **Laravel 12** dan frontend **Vue 3**.

| Keterangan  | Isi                                    |
| ----------- | -------------------------------------- |
| Nama        | Prihastomo Budi Satrio                 |
| NIM         | 24/534908/SV/24108                     |
| Kelas       | BB                                     |
| Mata Kuliah | Konstruksi dan Evolusi Perangkat Lunak |

## Struktur Repository

```
.
├── app/, routes/, database/, tests/   # backend Laravel 12 (PHP 8.2)
├── frontend/                          # frontend Vue 3 + Vue Router + Vitest
└── .github/workflows/ci.yml           # CI: Backend Test + Frontend Test
```

## Menjalankan di Lokal

### Backend

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve               # http://127.0.0.1:8000
```

### Frontend

```bash
cd frontend
npm ci
cp .env.example .env            # VITE_API_URL=/api
npm run dev                     # http://localhost:5173
```

Saat `npm run dev`, Vite meneruskan (proxy) setiap request `/api/*` ke Laravel di
`http://127.0.0.1:8000`, sehingga frontend dan API terlihat satu origin dan tidak
membutuhkan CORS. Pola yang sama dipakai Nginx di dalam container produksi.

Alur di frontend:

1. Halaman **Masuk** memanggil `POST /api/login` lewat Axios, lalu menyimpan
   `access_token` di `localStorage`.
2. Request interceptor Axios ([`src/lib/http.js`](frontend/src/lib/http.js)) menambahkan
   header `Authorization: Bearer <token>` ke setiap request.
3. Response interceptor menangani `401`: sesi dihapus dan user diarahkan ke halaman login.
4. Navigation guard Vue Router menolak membuka halaman ber-`meta.perluLogin` tanpa token.
5. Halaman **Peminjaman** menampilkan daftar, tambah, ubah, dan hapus data. Error validasi
   `422` dari Laravel ditampilkan di bawah field yang bersangkutan.

> Variabel berawalan `VITE_` ikut tertanam di hasil build dan bisa dibaca siapa pun lewat
> browser. Karena itu hanya alamat API yang ditaruh di sana, tidak pernah password atau token.

## RESTful API

Autentikasi memakai **Laravel Sanctum** (token). Setelah login, kirim header
`Authorization: Bearer <access_token>` pada setiap request. Semua respons berupa JSON.

Akun demo (dibuat oleh `UserSeeder`): `admin@kepl.test` / `password`.

| Metode | Endpoint | Auth | Respons |
| ------ | -------- | ---- | ------- |
| POST | `/api/login` | – | 200 token, 401 email/password salah, 422 input tidak valid |
| GET | `/api/me` | Bearer | 200 data user, 401 |
| POST | `/api/logout` | Bearer | 200, token dicabut |
| GET | `/api/peminjaman` | Bearer | 200 daftar peminjaman |
| POST | `/api/peminjaman` | Bearer | 201 data baru, 422 |
| GET | `/api/peminjaman/{id}` | Bearer | 200, 404 |
| PUT | `/api/peminjaman/{id}` | Bearer | 200, 404, 422 |
| DELETE | `/api/peminjaman/{id}` | Bearer | 200, 404 |

Tanpa token atau dengan token yang sudah dicabut, semua endpoint ber-Auth membalas
`401 {"message":"Unauthenticated."}`.

Contoh body `POST`/`PUT /api/peminjaman`:

```json
{
  "nama_peminjam": "Rina Kartika",
  "judul_buku": "Domain-Driven Design",
  "tanggal_pinjam": "2026-10-09",
  "tanggal_kembali": null,
  "status": "dipinjam"
}
```

### Postman

Import [`postman/KEPL-UTS-Peminjaman.postman_collection.json`](postman/KEPL-UTS-Peminjaman.postman_collection.json).
Variable `base_url` bernilai `http://127.0.0.1:8000/api`. Request **Login - 200 berhasil**
menyimpan token ke variable `token` secara otomatis, dan collection memakai Bearer Token
`{{token}}`, jadi seluruh collection bisa dijalankan berurutan lewat **Run collection**.

## Pengujian

```bash
vendor/bin/pint --test          # gaya kode PHP
php artisan test                # feature test Laravel

cd frontend
npm run lint                    # oxlint + ESLint
npm run test:unit -- --run      # unit test Vitest
npm run build                   # build produksi ke frontend/dist
```

## Alur Kerja Git

```
main  ← branch stabil, hanya menerima merge dari dev melalui Pull Request
 └── dev  ← branch integrasi, menerima merge dari feature/* melalui Pull Request
      └── feature/*  ← branch pengerjaan fitur
```

- `main` dan `dev` dilindungi *branch protection*: wajib Pull Request dan wajib lolos
  status check **Backend Test** dan **Frontend Test**.
- Pesan commit mengikuti [Conventional Commits](https://www.conventionalcommits.org/)
  (`feat:`, `fix:`, `test:`, `docs:`, `ci:`, `chore:`).

## Continuous Integration

Workflow [`.github/workflows/ci.yml`](.github/workflows/ci.yml) berjalan pada setiap push
dan setiap Pull Request menuju `main`/`dev`, berisi dua job yang berjalan paralel:

| Job | Isi |
| --- | --- |
| **Backend Test** | `composer install`, Laravel Pint, `php artisan test` |
| **Frontend Test** | `npm ci`, oxlint + ESLint, Vitest, `npm run build` |
