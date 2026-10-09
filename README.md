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
cp .env.example .env            # isi VITE_API_URL
npm run dev                     # http://localhost:5173
```

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
