# Jobsheet 8 — Koneksi PostgreSQL (Game Database)

Sub-CPMK: Menghubungkan aplikasi dengan basis data PostgreSQL.
Aplikasi mengelola data **senjata** dan **karakter** game.

## Perubahan dari Jobsheet 7
- Tambah `sql/database_kartu.sql` — DDL tabel `senjata` dan `karakter`.
- Tambah `includes/koneksi.php` — koneksi `PDO` driver `pgsql`.
- `senjata/proses_tambah.php` & `karakter/proses_tambah.php`: `$_SESSION[...][] = ...` (Jobsheet 7) diganti `INSERT ... RETURNING id` via prepared statement.
- `senjata/list.php` & `karakter/list.php`: sumber data diganti dari `$_SESSION` menjadi `SELECT * FROM ... ORDER BY id DESC`.
- `index.php`: kartu statistik Total Senjata/Karakter kini `SELECT COUNT(*)` dari database.

## Perbaikan tambahan
- Semua output database di-escape dengan helper `e()` (`includes/header.php`) untuk mencegah XSS.
- Validasi server untuk kedua form: angka harus bilangan bulat ≥ 0, teks maksimal 255 karakter, dan error database ditangkap (tidak lagi HTTP 500).
- Flash message memakai cookie berumur pendek (bukan `$_SESSION`) supaya aman di hosting serverless.
- Koneksi dapat dikonfigurasi lewat environment variable (lihat di bawah) tanpa mengubah kode.
- Tabel dibuat otomatis bila belum ada (`AUTO_MIGRATE=0` untuk mematikan).

## Cara menjalankan

### Opsi 1 — Lokal, PHP built-in server
1. Pastikan PostgreSQL berjalan dan ekstensi `pdo_pgsql` aktif (`php -m | grep pgsql`; bila belum, aktifkan `extension=pdo_pgsql` di `php.ini`).
2. Buat database:
   ```bash
   createdb game_database
   ```
3. (Opsional — tabel juga dibuat otomatis saat halaman pertama dibuka) jalankan skema:
   ```bash
   psql -d game_database -f sql/database_kartu.sql
   ```
4. Default koneksi: host `localhost`, port `5432`, database `game_database`, user `postgres`, password `postgres`. Sesuaikan lewat environment variable di bawah bila berbeda.
5. Jalankan:
   ```bash
   php -S localhost:8000
   ```
   Buka `http://localhost:8000/index.php`.

### Opsi 2 — Laragon (Apache)
Arahkan virtual host ke folder proyek ini (mis. `http://jobsheet08.test/`) atau taruh sebagai subfolder domain proyek. Path CSS/JS/link relatif otomatis (lihat `includes/header.php`).

### Opsi 3 — Docker (paling mudah, sudah termasuk PostgreSQL)
```bash
docker compose up --build
```
Buka `http://localhost:8080`.

### Opsi 4 — Vercel (serverless PHP + Neon PostgreSQL)
Vercel tidak mendukung PHP secara bawaan; proyek ini memakai runtime komunitas
[`vercel-php`](https://github.com/vercel-community/php) (`vercel.json`, PHP 8.3).
Semua request masuk lewat `api/index.php`, yang hanya meneruskan ke halaman dan aset di whitelist.

1. **Database:** siapkan PostgreSQL di Neon (gratis) — lewat Vercel: *Storage → Marketplace → Neon*, sambungkan ke project (otomatis mengisi `DATABASE_URL`); atau buat sendiri di neon.com lalu salin connection string-nya.
2. **Kode:** push isi folder ini (`vercel.json` harus di root repo) ke repository GitHub.
3. **Import:** Vercel → *Add New → Project* → pilih repo. Framework Preset: **Other**; Build/Output biarkan kosong.
4. **Node.js:** *Settings → General → Node.js Version* → **22.x**.
5. **Environment Variables:** `DATABASE_URL` (bila belum diisi integrasi), dan `APP_ENV=production`.
6. **Deploy** (atau *Redeploy* setelah mengubah env var). Tabel dibuat otomatis saat halaman pertama dibuka.

Uji lokal cara Vercel bekerja: `php -S localhost:8000 api/index.php`.

### Opsi 5 — Hosting Docker (Render, Railway, VPS)
Deploy sebagai Docker service memakai `Dockerfile`, dengan database PostgreSQL terkelola (Neon, Supabase, atau database bawaan platform). Set environment variable:

| Variabel | Fungsi | Default |
|---|---|---|
| `DATABASE_URL` | URL lengkap `postgres://user:pass@host:port/db?sslmode=require` (didahulukan bila ada) | — |
| `DB_HOST` `DB_PORT` `DB_NAME` `DB_USER` `DB_PASS` | Pengaturan terpisah | `localhost` `5432` `game_database` `postgres` `postgres` |
| `DB_SSLMODE` | mis. `require` untuk database cloud | kosong |
| `APP_ENV` | `production` menyembunyikan detail error koneksi | kosong |
| `AUTO_MIGRATE` | `0` untuk tidak membuat tabel otomatis | aktif |

## Catatan
- Data yang diinput **persisten** — tutup-buka browser, data tetap ada (beda dengan Jobsheet 7 yang hilang saat sesi berakhir).
- Query memakai prepared statement (`:nama_parameter`) — bukan concatenation string — sebagai fondasi keamanan yang diperdalam di Jobsheet 11.
- Kolom `id` sudah ikut ter-fetch dari `SELECT *`; akan dipakai untuk link Edit/Hapus mulai Jobsheet 9. Tombol Edit/Hapus saat ini masih tampilan saja.
