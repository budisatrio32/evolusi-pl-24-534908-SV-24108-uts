#!/bin/sh
# ===========================================================================
# Smoke test stack Docker Compose: memastikan alur Browser -> Nginx -> Laravel -> MySQL
# benar-benar bekerja sebelum image diterbitkan.
#
# Pemakaian:  sh docker/smoke-test.sh [alamat-frontend]
# Contoh:     sh docker/smoke-test.sh http://localhost:8080
# ===========================================================================
set -eu

BASE_URL="${1:-http://localhost:8080}"
EMAIL="${SMOKE_EMAIL:-admin@kepl.test}"
PASSWORD="${SMOKE_PASSWORD:-password}"

gagal() {
    echo "GAGAL: $1" >&2
    exit 1
}

echo "[1/5] Halaman frontend dapat dibuka"
curl -fsS -o /dev/null "${BASE_URL}/" || gagal "frontend tidak merespons"

echo "[2/5] Login lewat /api (Nginx meneruskan ke backend)"
token=$(curl -fsS -X POST "${BASE_URL}/api/login" \
    -H 'Accept: application/json' -H 'Content-Type: application/json' \
    -d "{\"email\":\"${EMAIL}\",\"password\":\"${PASSWORD}\"}" \
    | sed -n 's/.*"access_token":"\([^"]*\)".*/\1/p')
[ -n "${token}" ] || gagal "login tidak mengembalikan token"

echo "[3/5] Ambil data peminjaman dari MySQL dengan token"
curl -fsS "${BASE_URL}/api/peminjaman" \
    -H 'Accept: application/json' -H "Authorization: Bearer ${token}" \
    | grep -q '"data":\[' || gagal "daftar peminjaman tidak sesuai"

echo "[4/5] Tanpa token harus ditolak 401"
kode=$(curl -s -o /dev/null -w '%{http_code}' "${BASE_URL}/api/peminjaman" -H 'Accept: application/json')
[ "${kode}" = "401" ] || gagal "endpoint tanpa token membalas ${kode}, bukan 401"

echo "[5/5] Logout mencabut token"
curl -fsS -o /dev/null -X POST "${BASE_URL}/api/logout" \
    -H 'Accept: application/json' -H "Authorization: Bearer ${token}" || gagal "logout gagal"

echo "Smoke test lolos."
