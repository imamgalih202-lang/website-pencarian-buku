# Docker Setup for Website Pencarian Buku

Aplikasi ini telah dikonfigurasi menggunakan Docker untuk memudahkan deployment ke VPS.

## Persyaratan
- Docker
- Docker Compose

## Cara Menjalankan

1.  Buka terminal di folder `website-pencarian-buku`.
2.  Jalankan perintah berikut:
    ```bash
    docker-compose up -d
    ```
3.  Aplikasi akan berjalan di port **3500**.
    - Akses aplikasi: `http://localhost:3500`
    - Akses phpMyAdmin: `http://localhost:8081`

## Konfigurasi Database

Database akan **otomatis dibuat dan diisi** (migrasi & seeding) saat pertama kali Anda menjalankan `docker-compose up -d`. Anda tidak perlu melakukan import manual.

Jika Anda ingin melihat atau mengelola database:
1.  Buka phpMyAdmin di `http://localhost:8081`.
2.  Login dengan (jika diminta):
    - Server: `db`
    - Username: `root`
    - Password: `rootpassword`

## Catatan Deployment VPS
Jika dideploy ke VPS, pastikan untuk mengubah `BASE_URL` di file `docker-compose.yml` menjadi IP VPS atau domain Anda:
```yaml
environment:
  - BASE_URL=http://IP_VPS_ATAU_DOMAIN:3500/
```
Jangan lupa buka port **3500** dan **8081** di firewall VPS Anda.
