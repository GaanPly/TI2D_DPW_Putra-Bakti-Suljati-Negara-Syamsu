# Wireframe & User Flow — SIMPUS-Mini

Sub-CPMK: Merancang UI/UX aplikasi (proyek).

Halaman yang sudah ada (Beranda, Daftar/Tambah Buku, Daftar/Tambah Anggota — Jobsheet 1-3) belum mencakup fitur Login, Dashboard Petugas, dan Peminjaman/Pengembalian. Dokumen ini merancang wireframe dan alur pengguna (user flow) untuk halaman-halaman tersebut sebelum diimplementasikan mulai Jobsheet 5 dan seterusnya.

---

## 1. Identifikasi Aktor

Sistem Perpustakaan Mini (SIMPUS-Mini) mendefinisikan dua kelompok aktor utama:

1. **Tamu (Pengunjung Publik)**:
   - Tidak perlu melakukan login.
   - Hanya memiliki izin baca (*read-only*) untuk melihat katalog buku publik (Beranda dan Daftar Buku).
   - Tidak memiliki akses ke data anggota, form manipulasi data (tambah/edit/hapus buku), maupun transaksi sirkulasi peminjaman.

2. **Petugas Perpustakaan (Administrator)**:
   - Wajib login menggunakan kredensial (*username* dan *password*).
   - Memiliki hak akses penuh (*CRUD: Create, Read, Update, Delete*) terhadap katalog buku dan data anggota.
   - Memiliki hak eksklusif untuk menjalankan transaksi sirkulasi: mencatat peminjaman baru, memproses pengembalian buku, dan memeriksa riwayat peminjaman per anggota.

---

## 2. User Flow (Diagram Alur Pengguna)

### 2.1 User Flow — Peminjaman Buku

Alur normal saat petugas mencatat peminjaman buku oleh seorang anggota:

```
[Petugas Login] -> [Dashboard Petugas] -> [Pilih menu "Peminjaman Baru"]
        -> [Pilih Anggota dari Dropdown] 
        -> [Pilih Buku (hanya stok > 0)]
        -> [Klik "Simpan Peminjaman"] 
        -> [Sistem Mengurangi Stok Buku (-1)] 
        -> [Notifikasi Sukses & Kembali ke Dashboard]
```

### 2.2 User Flow — Pengembalian Buku

Alur saat petugas memproses pengembalian buku yang sedang dipinjam:

```
[Dashboard Petugas] -> [Pilih menu "Pengembalian"] 
        -> [Cari Transaksi Aktif (berdasarkan Nama Anggota / Judul Buku)]
        -> [Periksa Tanggal Jatuh Tempo & Kondisi Buku]
        -> [Klik tombol "Kembalikan"] 
        -> [Sistem Menambah Stok Buku (+1) & Mengubah Status jadi "Selesai"]
        -> [Kembali ke Dashboard / Refresh Tabel]
```

### 2.3 User Flow — Penanganan Skenario Khusus (Edge Cases)

#### Kasus A: Stok Buku Habis (`stok = 0`)
```
[Form Peminjaman] -> [Dropdown Buku]
        -> Buku bersangkutan berstatus [Disabled / Tidak Muncul]
        -> Pesan: "Buku sedang habis dipinjam"
        -> Petugas memilih judul lain yang tersedia
```

#### Kasus B: Anggota Memiliki Tunggakan / Keterlambatan Pengembalian
```
[Pilih Anggota] -> [Validasi Status Anggota]
        -> [Jika ada peminjaman aktif yang melewati batas tempo]
        -> [Tampilkan Peringatan: "Anggota memiliki pinjaman jatuh tempo"]
        -> [Form Peminjaman Terkunci hingga buku lama dikembalikan]
```

---

## 3. Wireframe Struktural (ASCII Layout)

### 3.1 Wireframe: Halaman Login Petugas

Halaman autentikasi untuk membatasi akses fitur administratif dan transaksi.

```
+-------------------------------------------------------------+
|                         SIMPUS-Mini                         |
+-------------------------------------------------------------+
|                                                             |
|                   +-----------------------+                 |
|                   |     Login Petugas     |                 |
|                   +-----------------------+                 |
|                   |                       |                 |
|                   | Username:             |                 |
|                   | [___________________] |                 |
|                   |                       |                 |
|                   | Password:             |                 |
|                   | [___________________] |                 |
|                   |                       |                 |
|                   | [     Masuk      ]    |                 |
|                   |                       |                 |
|                   | Kembali ke Beranda    |                 |
|                   +-----------------------+                 |
|                                                             |
+-------------------------------------------------------------+
|          &copy; 2026 SIMPUS-Mini — Sistem Perpustakaan       |
+-------------------------------------------------------------+
```

### 3.2 Wireframe: Dashboard Petugas

Halaman utama bagi petugas setelah autentikasi berhasil, menampilkan ringkasan data, tombol akses cepat, dan daftar peminjaman terkini.

```
+----------------------------------------------------------------------------------+
| SIMPUS-Mini    Beranda | Buku | Anggota | Peminjaman | Pengembalian | [Admin] Logout |
+----------------------------------------------------------------------------------+
|                                                                                  |
| Dashboard Petugas                                                                |
|                                                                                  |
| +--------------------+   +--------------------+   +--------------------+         |
| | Total Judul Buku   |   | Anggota Terdaftar  |   | Sedang Dipinjam    |         |
| |       12           |   |         8          |   |         3          |         |
| +--------------------+   +--------------------+   +--------------------+         |
|                                                                                  |
| Aksi Cepat:                                                                      |
| [ + Catat Peminjaman Baru ]     [ + Proses Pengembalian Buku ]                   |
|                                                                                  |
| Transaksi Peminjaman Terbaru                                                     |
| +------------------------------------------------------------------------------+ |
| | ID  | Nama Anggota     | Judul Buku          | Tgl Pinjam | Tempo  | Status  | |
| |-----+------------------+---------------------+------------+--------+---------| |
| | P01 | Siti Aminah      | Bumi Manusia        | 2026-09-01 | 7 Hari | Aktif   | |
| | P02 | Budi Santoso     | Belajar HTML & CSS  | 2026-09-03 | 7 Hari | Aktif   | |
| | P03 | Ahmad Dahlan     | Algoritma Dasar     | 2026-08-25 | 7 Hari | Selesai | |
| +------------------------------------------------------------------------------+ |
|                                                                                  |
+----------------------------------------------------------------------------------+
|          &copy; 2026 SIMPUS-Mini — Panel Petugas Perpustakaan                    |
+----------------------------------------------------------------------------------+
```

### 3.3 Wireframe: Form Peminjaman Buku

Form entri pencatatan sirkulasi peminjaman. Buku yang muncul pada pilihan hanya buku yang memiliki stok > 0.

```
+----------------------------------------------------------------------------------+
| SIMPUS-Mini    Beranda | Buku | Anggota | Peminjaman | Pengembalian | [Admin] Logout |
+----------------------------------------------------------------------------------+
|                                                                                  |
| Tambah Transaksi Peminjaman                                                      |
|                                                                                  |
| +------------------------------------------------------------------------------+ |
| | Nama Anggota:                                                                | |
| | [ [Pilih Anggota]                                                      v ]   | |
| |                                                                              | |
| | Judul Buku (Hanya Stok Tersedia):                                            | |
| | [ Laskar Pelangi (Tersedia: 4 eksemplar)                               v ]   | |
| |                                                                              | |
| | Tanggal Peminjaman:                                                          | |
| | [ 2026-09-06 (Auto hari ini)                                           ]     | |
| |                                                                              | |
| | Durasi Pinjam:                                                               | |
| | [ 7 Hari (Jatuh tempo: 2026-09-13)                                     ]     | |
| |                                                                              | |
| | [ Simpan Peminjaman ]   [ Batal ]                                            | |
| +------------------------------------------------------------------------------+ |
|                                                                                  |
+----------------------------------------------------------------------------------+
```

### 3.4 Wireframe: Form Pengembalian Buku

Antarmuka pencarian transaksi peminjaman aktif dan tombol eksekusi penyelesaian pengembalian.

```
+----------------------------------------------------------------------------------+
| SIMPUS-Mini    Beranda | Buku | Anggota | Peminjaman | Pengembalian | [Admin] Logout |
+----------------------------------------------------------------------------------+
|                                                                                  |
| Pengembalian Buku                                                                |
|                                                                                  |
| Cari Transaksi Peminjaman:                                                       |
| [ Ketik nama anggota / judul buku... ________________________ ] [ Cari ]         |
|                                                                                  |
| Transaksi Peminjaman Aktif:                                                      |
| +------------------------------------------------------------------------------+ |
| | No | Peminjam     | Buku Dipinjam  | Tgl Pinjam | Jatuh Tempo | Aksi         | |
| |----+--------------+----------------+------------+-------------+--------------| |
| | 1  | Siti Aminah  | Bumi Manusia   | 2026-09-01 | 2026-09-08  | [Kembalikan] | |
| | 2  | Budi Santoso | Web Praktis    | 2026-09-03 | 2026-09-10  | [Kembalikan] | |
| +------------------------------------------------------------------------------+ |
|                                                                                  |
+----------------------------------------------------------------------------------+
```

### 3.5 Wireframe: Riwayat Peminjaman per Anggota

Detail histori peminjaman seorang anggota beserta status pengembalian.

```
+----------------------------------------------------------------------------------+
| SIMPUS-Mini    Beranda | Buku | Anggota | Peminjaman | Pengembalian | [Admin] Logout |
+----------------------------------------------------------------------------------+
|                                                                                  |
| Riwayat Peminjaman: Siti Aminah (ID: AG-001)                                     |
|                                                                                  |
| +------------------------------------------------------------------------------+ |
| | Judul Buku       | Tgl Pinjam | Tgl Kembali | Denda   | Status               | |
| |------------------+------------+-------------+---------+----------------------| |
| | Laskar Pelangi   | 2026-08-01 | 2026-08-07  | Rp 0    | [Selesai]            | |
| | Struktur Data    | 2026-08-15 | 2026-08-22  | Rp 0    | [Selesai]            | |
| | Bumi Manusia     | 2026-09-01 | -           | Rp 0    | [Sedang Dipinjam]    | |
| +------------------------------------------------------------------------------+ |
|                                                                                  |
| [ < Kembali ke Daftar Anggota ]                                                  |
+----------------------------------------------------------------------------------+
```

---

## 4. Mockup Visual & Spesifikasi Desain (Style Guide)

Mengikuti konsistensi arsitektur CSS yang telah dibangun pada Jobsheet 2 dan Jobsheet 3 (`assets/css/style.css`):

### 4.1 Palet Warna (*Color Palette*)
- **Primary / Brand Theme**: `#1d5b8a` (Deep Blue)
  - Digunakan untuk latar belakang header, judul section/halaman (`<h2>`), border aksen, dan tombol utama (*submit*).
- **Primary Hover**: `#2c7bb6`
  - Digunakan saat kursor berada di atas navigasi link atau tombol submit.
- **Background Page**: `#f4f6f8` (Soft Light Grey)
  - Latar belakang keseluruhan dokumen `<body>` untuk menjaga kenyamanan mata.
- **Surface / Card Background**: `#ffffff` (Pure White)
  - Latar belakang blok konten `<section>`, kartu statistik, dan kontainer form/tabel.
- **Text Color Primary**: `#333333`
  - Teks konten paragraf, sel tabel, dan isian form.
- **Text Color Secondary / Subtitle**: `#666666`
- **Border / Divider**: `#e2e8f0` dan `#dddddd`
- **Status Colors (Aksen Bisnis)**:
  - Success / Tersedia / Selesai: `#27ae60` (Green)
  - Warning / Sedang Dipinjam / Edit: `#e67e22` (Orange)
  - Danger / Hapus / Jatuh Tempo: `#e74c3c` (Red)

### 4.2 Tipografi (*Typography*)
- **Font Family**: `'Segoe UI', Tahoma, Geneva, Verdana, sans-serif`
- **Hierarki Font**:
  - Judul Utama Brand (`header h1`): `1.5rem` (`24px`), `font-weight: 600`, warna `#ffffff`.
  - Judul Halaman / Section (`h2`): `1.3rem` (`20.8px`), `font-weight: 600`, warna `#1d5b8a`.
  - Judul Kartu (`article h3`): `1rem` (`16px`), `font-weight: 500`, warna `#555555`.
  - Angka Statistik (`article p`): `2rem` (`32px`), `font-weight: bold`, warna `#1d5b8a`.
  - Teks Dasar & Form Input: `0.95rem` – `1rem` (`15px` – `16px`), `line-height: 1.6`.

### 4.3 Desain Komponen UI
- **Tata Letak (Container)**:
  - Lebar maksimum kontainer utama (`main`): `960px`, terpusat secara horizontal (`margin: 20px auto;`).
- **Kartu Statistik (Dashboard & Beranda)**:
  - Menggunakan CSS Grid `repeat(3, 1fr)` dengan `gap: 15px`.
  - Memiliki `box-shadow: 0 2px 4px rgba(0,0,0,0.08)` dan `border-radius: 6px`.
  - Adaptif multi-breakpoint: 3 kolom (desktop), 2 kolom (tablet ≤768px), dan 1 kolom (ponsel ≤480px).
- **Navigasi Responsif**:
  - Menggunakan Flexbox pada desktop (`display: flex; justify-content: space-between;`).
  - Menu hamburger adaptif pada layar mobile (breakpoint ≤480px).
- **Tabel Data Responsif**:
  - Dibungkus dalam `<div class="table-responsive">` dengan properti `overflow-x: auto`.
  - Zebra striping pada baris genap: `tbody tr:nth-child(even) { background-color: #f8fafc; }`.
- **Form Controls**:
  - Label berukuran jelas dengan `display: block; margin-bottom: 5px; font-weight: bold;`.
  - Field input teks, password, dan dropdown select dengan `padding: 8px 10px; width: 100%; border: 1px solid #ccc; border-radius: 4px;`.

---

## 5. Hubungan dengan Implementasi Jobsheet Selanjutnya

Rancangan di atas menjadi cetak biru (*blueprint*) pengembangan bertahap pada jobsheet berikutnya:
1. **Jobsheet 5**: Menghidupkan interaktivitas sisi klien menggunakan JavaScript murni (validasi form client-side, dynamic DOM manipulation, filter tabel).
2. **Jobsheet 6**: Simulasi pengambilan data peminjaman asinkron (AJAX/Fetch API) dengan format JSON.
3. **Jobsheet 8–10**: Membangun backend PHP, skema relasional tabel peminjaman di database PostgreSQL, session login petugas, dan eksekusi transaksi peminjaman/pengembalian sungguhan.
