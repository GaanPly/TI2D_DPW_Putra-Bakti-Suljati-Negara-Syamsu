# Handbook SIMPUS-Mini (Jobsheet 6)

Panduan penggunaan dan pengembangan aplikasi **SIMPUS-Mini**, sistem perpustakaan mini berbasis HTML, CSS, dan JavaScript (tanpa framework dan tanpa backend).

---

## Daftar Isi

1. [Gambaran Umum](#1-gambaran-umum)
2. [Struktur Folder](#2-struktur-folder)
3. [Cara Menjalankan](#3-cara-menjalankan)
4. [Halaman Aplikasi](#4-halaman-aplikasi)
5. [Penjelasan JavaScript](#5-penjelasan-javascript)
6. [Format Data JSON](#6-format-data-json)
7. [Penjelasan CSS](#7-penjelasan-css)
8. [Daftar Perbaikan](#8-daftar-perbaikan)
9. [Pemecahan Masalah](#9-pemecahan-masalah)
10. [Keterbatasan dan Pengembangan Lanjutan](#10-keterbatasan-dan-pengembangan-lanjutan)

---

## 1. Gambaran Umum

SIMPUS-Mini adalah aplikasi front-end sederhana untuk mengelola data **buku** dan **anggota** perpustakaan.

| Fitur | Keterangan |
|---|---|
| Daftar Buku / Anggota | Data dimuat asinkron dari file JSON dengan `fetch()` |
| Pencarian real-time | Memfilter baris tabel saat mengetik |
| Tombol Muat Ulang | Mengambil ulang data buku (halaman Daftar Buku) |
| Hapus dengan konfirmasi | Menghapus baris dari tampilan (belum ke server) |
| Validasi form | Pengecekan input di sisi klien sebelum submit |
| Navigasi responsif | Menu hamburger di layar ≤ 480 px |

**Teknologi:** HTML5, CSS3 (Flexbox dan Grid), JavaScript ES6+ (`async/await`, `fetch`, event delegation).

---

## 2. Struktur Folder

```
Jobsheet6/
├── index.html                 # Beranda + ringkasan
├── anggota/
│   ├── list.html              # Daftar anggota
│   └── tambah.html            # Form tambah anggota
├── buku/
│   ├── list.html              # Daftar buku
│   └── tambah.html            # Form tambah buku
├── assets/
│   ├── css/style.css          # Seluruh gaya tampilan
│   └── js/
│       ├── app.js             # Fungsi umum (menu, hapus, filter, validasi)
│       ├── buku.js            # Memuat data buku
│       └── anggota.js         # Memuat data anggota
└── data/
    ├── buku.json
    └── anggota.json
```

Halaman di dalam subfolder (`buku/`, `anggota/`) memakai path relatif `../` untuk mengakses `assets/` dan `data/`.

---

## 3. Cara Menjalankan

> **Penting:** jangan membuka file dengan klik dua kali (`file://`). Browser memblokir `fetch()` ke file lokal sehingga tabel akan menampilkan *"Gagal memuat data: Failed to fetch"*. Jalankan lewat web server lokal.

### Opsi A: Live Server (VS Code)

1. Pasang ekstensi **Live Server**.
2. Buka folder `Jobsheet6` di VS Code.
3. Klik kanan `index.html`, lalu pilih **Open with Live Server**.

### Opsi B: Python

```bash
cd Jobsheet6
python -m http.server 8000
```

Buka `http://localhost:8000` di browser.

### Opsi C: Node.js

```bash
cd Jobsheet6
npx serve .
```

---

## 4. Halaman Aplikasi

| Halaman | Path | Fungsi |
|---|---|---|
| Beranda | `index.html` | Sambutan dan kartu ringkasan (angka masih statis) |
| Daftar Buku | `buku/list.html` | Tabel buku, pencarian judul, tombol Muat Ulang |
| Tambah Buku | `buku/tambah.html` | Form: judul, pengarang, tahun, ISBN, stok, kategori |
| Daftar Anggota | `anggota/list.html` | Tabel anggota, pencarian nama |
| Tambah Anggota | `anggota/tambah.html` | Form: nama, no. anggota, alamat, no. HP |

### Aturan validasi form

| Field | Aturan |
|---|---|
| Judul / Nama | Wajib diisi |
| Pengarang | Wajib diisi |
| Tahun | Angka 1900–2026 |
| Stok | Angka ≥ 0 |

Pesan error muncul di bawah field (elemen `<span class="error">`). Pesan hilang otomatis saat input sudah valid.

---

## 5. Penjelasan JavaScript

### 5.1 `assets/js/app.js` (dimuat di semua halaman)

| Fungsi | Peran |
|---|---|
| `initNavToggle()` | Menampilkan atau menyembunyikan menu saat tombol ☰ diklik (toggle class `nav-open`) |
| `initHapusConfirm()` | Event delegation pada `document`: klik `.btn-hapus` memunculkan `confirm()`, lalu menghapus baris |
| `terapkanFilter()` | Menyembunyikan baris tabel yang tidak mengandung kata kunci pada `#search-input` |
| `initTableFilter()` | Memasang event `input` pada kolom pencarian yang memanggil `terapkanFilter()` |
| `tampilkanError()` / `hapusError()` | Menambah dan menghapus pesan error di bawah input |
| `initValidasiForm()` | Memvalidasi `#form-tambah` saat submit; `preventDefault()` jika ada yang tidak valid |

> **Mengapa event delegation?** Baris tabel dibuat dinamis oleh `fetch()`, sehingga tombol Hapus belum ada saat halaman selesai dimuat. Listener dipasang pada `document` agar tetap berfungsi untuk elemen yang dibuat belakangan.

### 5.2 `assets/js/buku.js` dan `anggota.js`

Alur `muatDaftarBuku()` (dan `muatDaftarAnggota()`):

1. Tampilkan `#loading-indicator` dan kosongkan `<tbody>`.
2. Tunggu 600 ms (simulasi delay jaringan agar indikator terlihat).
3. `fetch("../data/....json")`; jika `!res.ok`, lempar error.
4. Parse JSON, lalu buat `<tr>` untuk tiap data dan tambahkan ke `<tbody>`.
5. Panggil `terapkanFilter()` agar kata kunci pencarian tetap berlaku.
6. Jika gagal, tampilkan baris pesan error. Bila halaman dibuka lewat `file://`, pesan berisi petunjuk memakai web server.
7. `finally`: sembunyikan indikator loading.

`buku.js` juga memasang listener pada `#btn-reload` untuk memanggil ulang `muatDaftarBuku()`.

---

## 6. Format Data JSON

### `data/buku.json`

```json
[
  { "judul": "Laskar Pelangi", "pengarang": "Andrea Hirata",
    "tahun": 2005, "kategori": "Fiksi", "stok": 4 }
]
```

| Key | Tipe | Keterangan |
|---|---|---|
| `judul` | string | Judul buku |
| `pengarang` | string | Nama pengarang |
| `tahun` | number | Tahun terbit |
| `kategori` | string | Jika kosong, ditampilkan `-` |
| `stok` | number | Jumlah eksemplar |

### `data/anggota.json`

```json
[
  { "no_anggota": "A001", "nama": "Siti Aminah",
    "alamat": "Malang", "no_hp": "0812xxxx" }
]
```

| Key | Tipe | Keterangan |
|---|---|---|
| `no_anggota` | string | Nomor anggota unik |
| `nama` | string | Nama lengkap |
| `alamat` | string | Alamat |
| `no_hp` | string | Nomor telepon |

Untuk menambah data, tambahkan objek baru di dalam array (jangan lupa koma antar objek), lalu muat ulang halaman.

---

## 7. Penjelasan CSS

`assets/css/style.css` disusun per bagian:

| Bagian | Teknik |
|---|---|
| Reset & Base | `box-sizing: border-box`, reset margin dan padding |
| Header & Navbar | Flexbox (`justify-content: space-between`, `flex-wrap`) |
| Main Layout | Lebar maks 1000 px, dipusatkan dengan `margin: auto` |
| Kartu Statistik | CSS Grid 3 kolom pada `section:nth-of-type(2)` di Beranda |
| Tabel | `border-collapse`, baris selang-seling, hover |
| Form | Input responsif (`max-width: 400px`) |
| Tabel Responsif | `.table-responsive { overflow-x: auto }` |
| Hamburger Menu | Tombol tersembunyi, tampil di ≤ 480 px |

### Breakpoint

| Lebar layar | Perubahan |
|---|---|
| ≤ 768 px | Kartu statistik menjadi 2 kolom |
| ≤ 480 px | Kartu 1 kolom, menu menjadi hamburger (vertikal), input form 100% lebar |

---

## 8. Daftar Perbaikan

| # | Berkas | Masalah | Perbaikan |
|---|---|---|---|
| 1 | `buku/list.html` | Struktur HTML rusak: `<div class="search-box">` menampung tombol dan loading, dan `id="loading-indicator"` muncul dua kali | Struktur dirapikan, ID hanya satu |
| 2 | `app.js`, `buku.js`, `anggota.js` | Setelah Muat Ulang semua baris tampil kembali padahal kotak pencarian masih terisi | Filter dipisah menjadi `terapkanFilter()` dan dijalankan ulang setelah data dirender |
| 3 | `app.js` | Pencarian memakai `keyup`, sehingga tempel teks lewat mouse tidak terfilter | Diganti ke event `input` |
| 4 | `buku.js` | `colspan="5"` padahal tabel buku punya 6 kolom | Diubah menjadi `colspan="6"` |
| 5 | Semua halaman | Menu navigasi tidak seragam (link Tambah Buku / Tambah Anggota hilang di sebagian halaman) | Menu disamakan di seluruh halaman |
| 6 | `buku.js`, `anggota.js` | Pesan "Failed to fetch" tidak menjelaskan penyebab | Pesan khusus untuk pembukaan via `file://` |

---

## 9. Pemecahan Masalah

| Gejala | Penyebab | Solusi |
|---|---|---|
| "Gagal memuat data: Failed to fetch" | Dibuka lewat `file://` | Jalankan lewat web server lokal ([bagian 3](#3-cara-menjalankan)) |
| "Gagal memuat data (status 404)" | Path atau nama file JSON salah | Pastikan `data/buku.json` dan `data/anggota.json` ada |
| "Unexpected token ... in JSON" | JSON tidak valid (koma berlebih atau kurang) | Cek dengan jsonlint.com |
| Tabel tetap kosong, tidak ada error | JSON berupa array kosong, atau kata kunci pencarian menyembunyikan semua baris | Kosongkan kotak pencarian |
| Tampilan tidak berubah setelah edit CSS/JS | Cache browser | Hard refresh (`Ctrl + Shift + R`) |
| Menu ☰ tidak muncul | Layar lebih lebar dari 480 px | Perkecil jendela browser |
| Form disubmit lalu halaman hanya memuat ulang | Form belum terhubung ke penyimpanan | Lihat [bagian 10](#10-keterbatasan-dan-pengembangan-lanjutan) |

Untuk melihat error teknis, buka **DevTools** (`F12`) lalu tab **Console** dan **Network**.

---

## 10. Keterbatasan dan Pengembangan Lanjutan

### Keterbatasan saat ini

- Form **Tambah Buku/Anggota** hanya divalidasi, datanya **belum disimpan**.
- Tombol **Edit** belum punya fungsi.
- **Hapus** hanya menghilangkan baris di tampilan dan kembali lagi saat halaman dimuat ulang.
- Angka di kartu **Ringkasan** Beranda (12, 8, 3) masih teks statis, tidak sesuai dengan isi JSON.
- Data dimasukkan ke tabel lewat `innerHTML`. Aman untuk data JSON milik sendiri, tetapi sebaiknya memakai `textContent` jika data berasal dari input pengguna.

### Ide pengembangan

1. Simpan data baru ke `localStorage` dan gabungkan dengan data JSON saat menampilkan daftar.
2. Hitung angka ringkasan Beranda dari `buku.json` dan `anggota.json` dengan `fetch()`.
3. Tambahkan fungsi Edit (isi ulang form dengan data baris terpilih).
4. Hubungkan ke backend (mis. Node.js/Express atau PHP + MySQL) agar data benar-benar persisten.
5. Tambahkan pengurutan kolom dan paginasi pada tabel.

---

*SIMPUS-Mini, Jobsheet 6.*
