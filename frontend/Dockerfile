# syntax=docker/dockerfile:1

# ===========================================================================
# Image frontend Vue 3 dengan multi-stage build:
#   build   -> Node.js menjalankan `npm ci` dan `npm run build` (menghasilkan dist/)
#   runtime -> Nginx non-root yang hanya berisi dist/ dan konfigurasi Nginx
# Node.js, node_modules, dan kode sumber tidak ikut ke image akhir.
# ===========================================================================
ARG NODE_IMAGE=node:24.21.0-alpine3.24
ARG NGINX_IMAGE=nginxinc/nginx-unprivileged:1.31.6-alpine3.24

# ---------------------------------------------------------------------------
# Tahap 1 - build
# ---------------------------------------------------------------------------
FROM ${NODE_IMAGE} AS build

WORKDIR /app

# Berkas dependency disalin lebih dulu: layer `npm ci` tetap diambil dari cache
# selama package.json dan package-lock.json tidak berubah.
COPY package.json package-lock.json .npmrc ./
RUN npm ci --no-audit --no-fund

COPY . .

# VITE_API_URL ikut tertanam di berkas JavaScript hasil build, jadi hanya boleh
# berisi nilai publik. "/api" berarti browser memanggil origin yang sama,
# lalu Nginx meneruskannya ke container backend.
ARG VITE_API_URL=/api
ENV VITE_API_URL=${VITE_API_URL}

RUN npm run build

# ---------------------------------------------------------------------------
# Tahap 2 - runtime
# ---------------------------------------------------------------------------
FROM ${NGINX_IMAGE} AS runtime

# Template diproses envsubst saat container start menjadi /etc/nginx/conf.d/default.conf.
COPY nginx/default.conf.template /etc/nginx/templates/default.conf.template

COPY --from=build /app/dist /usr/share/nginx/html

# Alamat backend di jaringan Docker. Nama "backend" adalah nama service di
# docker-compose.yml; bisa diganti saat run tanpa build ulang.
ENV API_UPSTREAM=http://backend:8000

# Image nginx-unprivileged berjalan sebagai user nginx (uid 101) di port 8080.
EXPOSE 8080

HEALTHCHECK --interval=10s --timeout=3s --start-period=5s --retries=3 \
    CMD wget -q --spider http://127.0.0.1:8080/healthz || exit 1
