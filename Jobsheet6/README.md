# Handbook Jobsheet 6: SIMPUS-Mini dengan `fetch()` dan JSON

Panduan belajar dan referensi untuk **Jobsheet 6**: mengubah tabel statis di aplikasi perpustakaan **SIMPUS-Mini** menjadi tabel yang diisi **secara dinamis** dari file JSON memakai `fetch()`, `async`/`await`, dan `try`/`catch`/`finally`.

> **Baca ini dulu:** jobsheet ini **tidak bisa** dijalankan dengan klik dua kali pada file HTML. `fetch()` butuh alamat `http://`, bukan `file://`. Lihat [Cara Menjalankan](#2-cara-menjalankan) sebelum mencoba apa pun.

---

## Daftar Isi

1. [Tujuan dan Gambaran Umum](#1-tujuan-dan-gambaran-umum)
2. [Cara Menjalankan](#2-cara-menjalankan)
3. [Struktur Proyek](#3-struktur-proyek)
4. [Konsep Dasar: AJAX, JSON, `fetch()`, Promise, `async`/`await`, `try`/`catch`/`finally`](#4-konsep-dasar)
5. [Apa yang Berubah di File HTML](#5-apa-yang-berubah-di-file-html)
6. [Data JSON](#6-data-json)
7. [JS: Mengambil dan Menampilkan Daftar Buku](#7-js-mengambil-dan-menampilkan-daftar-buku)
8. [JS: Mengambil dan Menampilkan Daftar Anggota](#8-js-mengambil-dan-menampilkan-daftar-anggota)
9. [JS: Event Delegation pada Tombol Hapus](#9-js-event-delegation-pada-tombol-hapus)
10. [JS: Pencarian Tabel yang Bekerja dengan Data Dinamis](#10-js-pencarian-tabel-yang-bekerja-dengan-data-dinamis)
11. [Server Lokal dan CORS](#11-server-lokal-dan-cors)
12. [Catatan Perbaikan pada Kode](#12-catatan-perbaikan-pada-kode)
13. [Rangkuman](#13-rangkuman)
14. [Latihan Lanjutan](#14-latihan-lanjutan)
15. [Pemecahan Masalah](#15-pemecahan-masalah)

---

## 1. Tujuan dan Gambaran Umum

Pada jobsheet sebelumnya, baris tabel ditulis manual di HTML. Di Jobsheet 6, `<tbody>` dikosongkan dan baris-barisnya dibuat oleh JavaScript setelah mengambil data dari file JSON.

Setelah mempelajari handbook ini, kamu mampu:

- menjelaskan AJAX, JSON, Promise, dan `async`/`await`;
- mengambil data dari file JSON dengan `fetch()` dan memeriksa keberhasilannya lewat `res.ok`;
- menampilkan data ke tabel secara dinamis dengan `createElement` dan `appendChild`;
- menangani kegagalan dengan `try`/`catch`/`finally` dan menampilkan loading indicator;
- memakai **event delegation** untuk elemen yang dibuat dinamis;
- menjalankan proyek lewat server lokal dan memahami kenapa CORS membatasi `file://`.

### Fitur aplikasi

| Fitur | Keterangan |
|---|---|
| Daftar Buku / Anggota | Data dimuat dari `data/*.json` dengan `fetch()` |
| Loading indicator | Teks "Memuat data..." tampil selama data diambil |
| Penanganan error | Pesan error tampil rapi di dalam tabel |
| Tombol **Muat Ulang** | Mengambil ulang data buku tanpa me-reload halaman |
| Pencarian real-time | Memfilter baris tabel saat mengetik |
| Hapus dengan konfirmasi | Menghapus baris dari tampilan (front-end saja) |
| Validasi form | Pengecekan input di sisi klien |
| Menu hamburger | Menu responsif pada layar ≤ 480 px |

---

## 2. Cara Menjalankan

Beda dari jobsheet 1 sampai 5, **jobsheet ini wajib dijalankan lewat server lokal**.

### Cara 1: Live Server (VS Code)

1. Pasang ekstensi **Live Server**.
2. Buka folder `Jobsheet6` di VS Code.
3. Klik kanan `index.html`, lalu pilih **Open with Live Server**.

### Cara 2: PHP

```bash
cd Jobsheet6
php -S localhost:8000
```

Buka `http://localhost:8000/index.html`.

### Cara 3: Python

```bash
cd Jobsheet6
python -m http.server 8000
```

Buka `http://localhost:8000/index.html`.

### Cara 4: Laragon (Apache)

Taruh folder proyek di `C:\laragon\www\`, nyalakan Apache lewat Laragon, lalu buka `http://<nama-folder>.test/...` di browser.

### Cara 5: Node.js

```bash
cd Jobsheet6
npx serve .
```

Semua cara bertujuan sama: membuka halaman lewat `http://...`, bukan `file://...`. Penjelasan lengkap ada di [bab 11](#11-server-lokal-dan-cors).

---

## 3. Struktur Proyek

```
Jobsheet6/
├── index.html                  # Beranda + ringkasan
├── anggota/
│   ├── list.html               # Daftar anggota (diisi via fetch)
│   └── tambah.html             # Form tambah anggota
├── buku/
│   ├── list.html               # Daftar buku (diisi via fetch)
│   └── tambah.html             # Form tambah buku
├── assets/
│   ├── css/style.css
│   └── js/
│       ├── app.js              # Fungsi umum: menu, hapus, filter, validasi
│       ├── buku.js             # fetch + render Daftar Buku
│       └── anggota.js          # fetch + render Daftar Anggota
└── data/
    ├── buku.json
    └── anggota.json
```

**Urutan pemuatan script** di halaman daftar: `app.js` dulu, baru `buku.js` atau `anggota.js`. Halaman `tambah.html` dan Beranda hanya memuat `app.js` karena tidak punya tabel dinamis.

---

## 4. Konsep Dasar

Enam konsep ini perlu dipahami sebelum membaca kode `buku.js` dan `anggota.js`.

### 4.1 AJAX

**AJAX** (*Asynchronous JavaScript and XML*) adalah teknik mengambil data dari server **tanpa me-reload seluruh halaman**. Namanya historis; sekarang hampir selalu dipakai dengan JSON, bukan XML. Dengan AJAX, JavaScript mengambil data di latar belakang lalu memperbarui **sebagian** halaman saja (di sini: isi `<tbody>`).

### 4.2 JSON

**JSON** (*JavaScript Object Notation*) adalah format teks untuk data terstruktur yang mudah dibaca manusia dan program.

```json
[
    { "judul": "Laskar Pelangi", "pengarang": "Andrea Hirata", "tahun": 2005, "kategori": "Fiksi", "stok": 4 }
]
```

- `[ ]` menandakan **array** (daftar item).
- `{ }` menandakan **objek**, kumpulan pasangan **kunci** dan **nilai**.
- Satu objek JSON membawa informasi yang sama dengan satu baris `<tr>` di tabel.

> JSON lebih ketat daripada objek JavaScript: nama kunci **wajib** memakai tanda kutip dua (`"judul"`), dan JSON **tidak boleh** berisi komentar atau koma di elemen terakhir.

### 4.3 `fetch()`

Fungsi bawaan browser untuk **meminta** data dari sebuah alamat.

```js
fetch("../data/buku.json")
```

Baris ini hanya **memulai** permintaan; hasilnya berupa Promise, bukan data.

### 4.4 Promise dan `await`

Mengambil data butuh waktu. JavaScript tidak berhenti menunggu (halaman akan membeku), jadi `fetch()` mengembalikan **Promise**: janji bahwa hasil akan tersedia **nanti**. Kata kunci **`await`** berarti "tunggu Promise ini selesai, baru lanjut":

```js
const res = await fetch("../data/buku.json");
```

### 4.5 `async function`

`await` hanya boleh dipakai di dalam fungsi yang ditandai `async`:

```js
async function muatDaftarBuku() {
    // boleh pakai await di sini
}
```

`async`/`await` adalah cara modern yang lebih mudah dibaca dibanding `.then()`/`.catch()` berantai.

### 4.6 `try` / `catch` / `finally`

```js
try {
    // kode yang mungkin gagal
} catch (err) {
    // dijalankan hanya jika ada error di try
} finally {
    // selalu dijalankan, berhasil atau gagal
}
```

| Blok | Fungsi |
|---|---|
| `try` | Membungkus kode yang berpotensi gagal |
| `catch (err)` | Menangkap error agar program tidak crash; tampilkan pesan ramah |
| `finally` | Selalu jalan; cocok untuk menyembunyikan loading indicator |

---

## 5. Apa yang Berubah di File HTML

### 5.1 `<tbody>` kosong

Dulu `<tbody>` berisi baris `<tr>` manual. Sekarang:

```html
<tbody>
    <!-- Baris diisi dinamis oleh assets/js/buku.js via fetch('../data/buku.json') -->
</tbody>
```

Tanpa JavaScript, tabel tampil kosong (hanya `<thead>` yang terlihat).

### 5.2 Loading indicator

```html
<p id="loading-indicator" style="display:none;">Memuat data...</p>
```

- `id="loading-indicator"` adalah "kait" yang dicari JavaScript lewat `getElementById`.
- `style="display:none;"` adalah **inline style**: elemen disembunyikan sejak awal dan hanya ditampilkan JavaScript selama proses fetch. Karena statusnya terus diubah lewat JS, inline style lebih praktis daripada membuat aturan baru di `style.css`.

> **Penting:** `id` harus **unik** dalam satu halaman. Pada versi awal `buku/list.html`, ID ini muncul dua kali (lihat [bab 12](#12-catatan-perbaikan-pada-kode)).

### 5.3 Tombol Muat Ulang (halaman Daftar Buku)

```html
<p style="margin-bottom: 1rem;">
    <button type="button" id="btn-reload">Muat Ulang</button>
</p>
```

### 5.4 Urutan tag `<script>`

```html
<script src="../assets/js/app.js"></script>
<script src="../assets/js/buku.js"></script>
```

Script dimuat berurutan. `app.js` (fungsi umum) selalu lebih dulu, baru file khusus halaman.

### 5.5 Kenapa `buku.js` dan `anggota.js` dipisah dari `app.js`?

`app.js` berisi fungsi yang relevan di **banyak halaman** (menu, validasi, filter). Logika `fetch` bersifat **spesifik** per jenis data. Memisahkannya membuat file lebih pendek, mudah dicari, dan halaman yang tidak butuh (mis. Beranda) tidak memuat kode yang tidak relevan.

---

## 6. Data JSON

### 6.1 `data/buku.json` (10 buku)

```json
[
    { "judul": "Laskar Pelangi", "pengarang": "Andrea Hirata", "tahun": 2005, "kategori": "Fiksi", "stok": 4 },
    { "judul": "Bumi Manusia", "pengarang": "Pramoedya Ananta Toer", "tahun": 1980, "kategori": "Fiksi", "stok": 2 },
    { "judul": "Negeri 5 Menara", "pengarang": "Ahmad Fuadi", "tahun": 2009, "kategori": "Fiksi", "stok": 0 },
    { "judul": "Filosofi Teras", "pengarang": "Henry Manampiring", "tahun": 2018, "kategori": "Non-Fiksi", "stok": 5 },
    { "judul": "Ronggeng Dukuh Paruk", "pengarang": "Ahmad Tohari", "tahun": 1982, "kategori": "Fiksi", "stok": 1 },
    { "judul": "Cantik Itu Luka", "pengarang": "Eka Kurniawan", "tahun": 2002, "kategori": "Fiksi", "stok": 3 },
    { "judul": "Pulang", "pengarang": "Tere Liye", "tahun": 2015, "kategori": "Fiksi", "stok": 2 },
    { "judul": "Sang Pemimpi", "pengarang": "Andrea Hirata", "tahun": 2006, "kategori": "Fiksi", "stok": 6 },
    { "judul": "Perahu Kertas", "pengarang": "Dee Lestari", "tahun": 2009, "kategori": "Fiksi", "stok": 0 },
    { "judul": "Gadis Kretek", "pengarang": "Ratih Kumala", "tahun": 2012, "kategori": "Fiksi", "stok": 4 }
]
```

### 6.2 `data/anggota.json` (4 anggota)

```json
[
    { "no_anggota": "A001", "nama": "Siti Aminah", "alamat": "Malang", "no_hp": "0812xxxx" },
    { "no_anggota": "A002", "nama": "Budi Santoso", "alamat": "Batu", "no_hp": "0813xxxx" },
    { "no_anggota": "A003", "nama": "Dewi Lestari", "alamat": "Malang", "no_hp": "0814xxxx" },
    { "no_anggota": "A004", "nama": "Rizky Firmansyah", "alamat": "Lawang", "no_hp": "0815xxxx" }
]
```

### 6.3 Nama kunci sama dengan atribut `name` di form

Kunci JSON (`judul`, `pengarang`, `tahun`, `stok`; `no_anggota`, `nama`, `alamat`, `no_hp`) **sama persis** dengan atribut `name` pada input form tambah. Ini disengaja: satu field selalu punya satu nama di setiap lapisan (HTML, JSON, nanti database), sehingga mudah dilacak dan tidak perlu "diterjemahkan".

### 6.4 Tipe data di JSON

| Tipe | Contoh | Ciri |
|---|---|---|
| **String** | `"Laskar Pelangi"`, `"A001"` | Diapit tanda kutip dua |
| **Number** | `2005`, `4`, `0` | **Tanpa** tanda kutip |

- `"tahun": 2005` adalah angka sungguhan. Jika ditulis `"2005"` (dengan kutip), nilainya menjadi teks dan perlu `parseInt()` untuk perbandingan angka.
- `"no_anggota": "A001"` sengaja berupa **string** karena mengandung huruf.

### 6.5 Dari teks JSON menjadi objek JavaScript

File `.json` hanyalah **teks**. Ia baru menjadi objek yang bisa diakses lewat `buku.judul` setelah **diuraikan (parse)** oleh `res.json()` (lihat [bab 7.5](#75-mengambil-data-dan-memeriksa-keberhasilannya)).

---

## 7. JS: Mengambil dan Menampilkan Daftar Buku

Fungsi paling penting di jobsheet ini; menggabungkan semua konsep dari [bab 4](#4-konsep-dasar).

### 7.1 Kode lengkap (`assets/js/buku.js`)

```js
// Mengambil & menampilkan Daftar Buku secara asinkron dari data/buku.json
async function muatDaftarBuku() {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        // simulasi delay jaringan agar loading indicator terlihat
        await new Promise((resolve) => setTimeout(resolve, 600));

        const res = await fetch("../data/buku.json");
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }
        const daftarBuku = await res.json();

        daftarBuku.forEach(function (buku) {
            const tr = document.createElement("tr");
            tr.innerHTML =
                "<td>" + buku.judul + "</td>" +
                "<td>" + buku.pengarang + "</td>" +
                "<td>" + buku.tahun + "</td>" +
                "<td>" + (buku.kategori || "-") + "</td>" +
                "<td>" + buku.stok + "</td>" +
                "<td>" +
                "<button type=\"button\">Edit</button> " +
                "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                "</td>";
            tbody.appendChild(tr);
        });
        if (typeof terapkanFilter === "function") terapkanFilter();
    } catch (err) {
        if (location.protocol === "file:") {
            err.message = "halaman dibuka lewat file://. Jalankan lewat web server " +
                "(mis. ekstensi Live Server di VS Code, atau `python -m http.server`) lalu buka http://localhost:...";
        }
        tbody.innerHTML =
            "<tr><td colspan=\"6\">Gagal memuat data: " + err.message + "</td></tr>";
    } finally {
        loading.style.display = "none";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarBuku(); // Muat data pertama kali saat web dibuka

    // Event listener tombol Muat Ulang
    const btnReload = document.getElementById("btn-reload");
    if (btnReload) {
        btnReload.addEventListener("click", function () {
            muatDaftarBuku();
        });
    }
});
```

### 7.2 Mengambil elemen yang dibutuhkan

```js
const tbody = document.querySelector(".table-responsive table tbody");
const loading = document.getElementById("loading-indicator");
if (!tbody) return;
```

- `tbody`: `<tbody>` kosong tempat baris hasil fetch disisipkan.
- `loading`: indikator "Memuat data...".
- `if (!tbody) return;` adalah **guard clause**. Hanya `tbody` yang diperiksa (bukan `loading`); ini aman karena `buku.js` hanya dimuat di halaman yang pasti punya kedua elemen tersebut.

### 7.3 Menampilkan loading indicator

```js
loading.style.display = "block";
tbody.innerHTML = "";
```

- Baris pertama menimpa `display:none` dari HTML sehingga teks "Memuat data..." muncul.
- Baris kedua **mengosongkan** `<tbody>` agar fungsi aman dipanggil berulang (dipakai oleh tombol **Muat Ulang**).

### 7.4 Simulasi delay jaringan

```js
await new Promise((resolve) => setTimeout(resolve, 600));
```

Membuat Promise manual yang menunda 600 ms, **murni untuk belajar**: file JSON lokal terlalu cepat dimuat sehingga tanpa delay ini, loading indicator tidak sempat terlihat. Pada aplikasi sungguhan, baris ini dihapus.

### 7.5 Mengambil data dan memeriksa keberhasilannya

```js
const res = await fetch("../data/buku.json");
if (!res.ok) {
    throw new Error("Gagal mengambil data (status " + res.status + ")");
}
const daftarBuku = await res.json();
```

- **Path `../data/buku.json`**: `buku.js` dimuat dari `buku/list.html`, jadi perlu naik satu folder (`../`) untuk mencapai `data/`.
- **`res.ok`**: `true` jika status HTTP 200-an. `fetch()` **tidak otomatis gagal** saat file tidak ditemukan (404), maka pengecekan manual ini penting.
- **`throw new Error(...)`**: sengaja melempar error agar alur pindah ke `catch`, mengubah "kegagalan logis" menjadi penanganan error yang seragam.
- **`await res.json()`**: mengubah teks JSON menjadi array objek JavaScript. Ia juga memakai `await` karena membaca dan menguraikan teks adalah proses asinkron terpisah.

### 7.6 Membuat baris tabel dari data

```js
daftarBuku.forEach(function (buku) {
    const tr = document.createElement("tr");
    tr.innerHTML = "<td>" + buku.judul + "</td>" + /* ... */;
    tbody.appendChild(tr);
});
```

- `forEach` mengulang setiap objek buku di array.
- `document.createElement("tr")` membuat elemen `<tr>` baru dari kode.
- `tr.innerHTML = "..."` mengisi `<tr>` dengan 6 sel `<td>`, termasuk tombol Edit dan Hapus. `buku.judul`, `buku.pengarang`, dst. mengakses **nilai** dari kunci di objek.
- `(buku.kategori || "-")` menampilkan tanda `-` jika kategori kosong.
- `tbody.appendChild(tr)` menambahkan baris sebagai anak terakhir `<tbody>`. Setelah seluruh `forEach`, `<tbody>` berisi 10 baris.

> **Catatan keamanan (XSS):** menggabung teks langsung ke `innerHTML` berisiko jika datanya berasal dari input pengguna yang tidak tepercaya. Di jobsheet ini aman karena `buku.json` dikontrol developer sendiri. Untuk data dari pengguna, gunakan `textContent` atau `createElement` per sel.

### 7.7 Menangkap dan menampilkan error

```js
} catch (err) {
    tbody.innerHTML =
        "<tr><td colspan=\"6\">Gagal memuat data: " + err.message + "</td></tr>";
}
```

- Menangkap kegagalan `fetch` itu sendiri (mis. nama file salah) maupun `throw new Error` dari [7.5](#75-mengambil-data-dan-memeriksa-keberhasilannya).
- **`colspan="6"`** membuat satu sel merentang selebar **6 kolom** (jumlah kolom tabel buku) agar pesan tampil sebagai satu baris penuh.
- Jika halaman dibuka lewat `file://`, pesan diganti dengan petunjuk memakai web server (lihat [bab 11](#11-server-lokal-dan-cors)).

### 7.8 Blok `finally`: selalu menyembunyikan loading

```js
} finally {
    loading.style.display = "none";
}
```

Blok `finally` jalan **baik berhasil maupun gagal**. Jika baris ini diletakkan di akhir `try`, ia tidak akan pernah dijalankan saat error (eksekusi melompat ke `catch`), dan loading indicator akan **macet tampil selamanya**.

### 7.9 Memanggil fungsi saat halaman siap

```js
document.addEventListener("DOMContentLoaded", function () {
    muatDaftarBuku();
    // ... listener tombol Muat Ulang
});
```

`muatDaftarBuku` baru dipanggil setelah HTML selesai dimuat menjadi DOM, sehingga `<tbody>` dan `#loading-indicator` pasti sudah ada.

---

## 8. JS: Mengambil dan Menampilkan Daftar Anggota

`anggota.js` punya struktur **identik** dengan `buku.js`. Berikut bagian yang berbeda.

### 8.1 Perbandingan

| Bagian | `buku.js` | `anggota.js` |
|---|---|---|
| Nama fungsi | `muatDaftarBuku` | `muatDaftarAnggota` |
| Sumber data | `../data/buku.json` | `../data/anggota.json` |
| Variabel array | `daftarBuku` | `daftarAnggota` |
| Variabel per-item | `buku` | `anggota` |
| Kunci yang diakses | `.judul`, `.pengarang`, `.tahun`, `.kategori`, `.stok` | `.no_anggota`, `.nama`, `.alamat`, `.no_hp` |
| Jumlah kolom (`colspan`) | 6 | 5 |
| Tombol Muat Ulang | Ada | Tidak ada |

### 8.2 Potongan kode yang berbeda

```js
daftarAnggota.forEach(function (anggota) {
    const tr = document.createElement("tr");
    tr.innerHTML =
        "<td>" + anggota.no_anggota + "</td>" +
        "<td>" + anggota.nama + "</td>" +
        "<td>" + anggota.alamat + "</td>" +
        "<td>" + anggota.no_hp + "</td>" +
        "<td>" +
        "<button type=\"button\">Edit</button> " +
        "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
        "</td>";
    tbody.appendChild(tr);
});
```

### 8.3 Kenapa nama kunci harus persis sama?

Jika salah ketik (mis. `anggota.nomor` padahal kuncinya `no_anggota`), JavaScript **tidak error**. Ia hanya mengembalikan `undefined`, sehingga sel tampil kosong atau bertuliskan "undefined". Kesalahan ini mudah terlewat karena tidak ada pesan di Console. Jika data tampil aneh, **periksa dulu nama kunci** di kode dan di JSON.

### 8.4 Kenapa tidak dibuat satu fungsi generik?

Dua fungsi eksplisit lebih mudah dibaca dan ditelusuri untuk pemula. Menyatukannya menjadi satu fungsi yang menerima nama file dan daftar kunci adalah latihan lanjutan yang bagus (lihat [bab 14](#14-latihan-lanjutan)).

---

## 9. JS: Event Delegation pada Tombol Hapus

### 9.1 Sebelum dan sesudah

**Jobsheet 5 (listener per tombol):**

```js
function initHapusConfirm() {
    document.querySelectorAll(".btn-hapus").forEach(function (btn) {
        btn.addEventListener("click", function () {
            // ... confirm lalu row.remove()
        });
    });
}
```

**Jobsheet 6 (event delegation):**

```js
function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-hapus");
        if (!btn) return;

        const row = btn.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
        if (yakin && row) {
            row.remove();
        }
    });
}
```

### 9.2 Masalah yang diperbaiki

Versi lama memasang listener ke semua `.btn-hapus` **yang sudah ada saat `DOMContentLoaded`**. Di jobsheet ini `<tbody>` kosong; tombol Hapus baru dibuat **setelah** `fetch` selesai (bahkan ditambah delay 600 ms). Akibatnya saat `querySelectorAll` dijalankan, **belum ada satu pun tombol**, tidak ada listener yang terpasang, dan tombol Hapus tidak akan bereaksi.

### 9.3 Solusi: event delegation

Pasang **satu** listener di elemen leluhur yang selalu ada (`document`), memanfaatkan **event bubbling**: saat tombol diklik, kejadian "menjalar" naik (tombol → `<td>` → `<tr>` → `<tbody>` → ... → `document`).

| Bagian | Penjelasan |
|---|---|
| `document.addEventListener("click", ...)` | Satu listener, dipasang sekali, di elemen yang selalu ada |
| `e.target` | Elemen paling spesifik yang diklik pengguna |
| `e.target.closest(".btn-hapus")` | Mencari ke atas untuk memastikan klik terjadi pada (atau di dalam) tombol Hapus |
| `if (!btn) return;` | Abaikan klik di tempat lain (tombol Edit, kolom pencarian, dll.) |

### 9.4 Kenapa berhasil untuk tombol yang "belum ada"?

Listener di `document` **tidak peduli kapan** tombol dibuat. Selama tombol ada di DOM saat diklik, klik itu menjalar ke `document` dan terdeteksi lewat `closest(".btn-hapus")`.

### 9.5 Kapan memakai event delegation?

| Situasi | Pendekatan |
|---|---|
| Elemen sudah ada sejak halaman dimuat, jumlah tetap | Listener langsung ke tiap elemen |
| Elemen dibuat/dihapus dinamis setelah halaman dimuat | Event delegation di leluhur yang stabil |

---

## 10. JS: Pencarian Tabel yang Bekerja dengan Data Dinamis

Pencarian di `app.js` juga harus memperhitungkan baris yang dibuat belakangan. Kode ini dipecah menjadi dua fungsi:

```js
function terapkanFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    const keyword = input.value.toLowerCase();
    table.querySelectorAll("tbody tr").forEach(function (row) {
        const teks = row.textContent.toLowerCase();
        row.style.display = teks.includes(keyword) ? "" : "none";
    });
}

function initTableFilter() {
    const input = document.getElementById("search-input");
    if (!input) return;
    input.addEventListener("input", terapkanFilter);
}
```

| Fungsi | Peran |
|---|---|
| `terapkanFilter()` | Menyembunyikan baris yang tidak mengandung kata kunci |
| `initTableFilter()` | Memasang event `input` pada kolom pencarian |

`terapkanFilter()` juga **dipanggil `buku.js` dan `anggota.js` setelah baris selesai dirender**. Dengan begitu, jika kotak pencarian sedang terisi lalu data dimuat ulang (tombol **Muat Ulang**), tabel tetap konsisten dengan kata kunci yang tampil.

Event `input` dipilih menggantikan `keyup` karena juga menangkap teks yang ditempel (paste) atau diubah lewat mouse.

---

## 11. Server Lokal dan CORS

### 11.1 Gejala jika dibuka lewat `file://`

Tabel menampilkan **"Gagal memuat data: Failed to fetch"** (atau macet pada "Memuat data..."). Ini **bukan bug kode**, melainkan pembatasan keamanan browser.

### 11.2 Apa itu CORS?

**CORS** (*Cross-Origin Resource Sharing*) adalah kebijakan keamanan browser yang membatasi `fetch()` antar **origin**. Saat dibuka dari File Explorer, alamat halaman berbentuk:

```
file:///D:/Kuliah/PemrogramanWeb/jobsheet-06/buku/list.html
```

Protokol `file://` diperlakukan **sangat ketat**: banyak browser (terutama Chrome) tidak mengizinkan `fetch()` mengambil file lain lewat protokol ini, walau filenya bersebelahan di komputer yang sama. Tag `<link>` dan `<script src>` tidak terkena pembatasan seketat itu.

### 11.3 Solusi

Buka halaman lewat `http://` dengan server lokal (lihat [bab 2](#2-cara-menjalankan)).

### 11.4 Kenapa baru muncul sekarang?

Jobsheet 1 sampai 5 tidak memakai `fetch()` untuk mengambil file lain, jadi aman dibuka lewat `file://`. Di jobsheet 6, `fetch()` pertama kali dipakai, sehingga batasan CORS pertama kali "terasa". Keduanya selalu berkaitan dalam pengembangan web sungguhan.

### 11.5 Cara menguji penanganan error

1. Di `assets/js/buku.js`, ubah `fetch("../data/buku.json")` menjadi `fetch("../data/bukuu.json")` (sengaja salah).
2. Simpan, lalu buka `buku/list.html` lewat server lokal.
3. Pesan **"Gagal memuat data: Gagal mengambil data (status 404)"** muncul di tabel; ini membuktikan `try`/`catch` bekerja.
4. **Kembalikan** nama file ke `buku.json`.

---

## 12. Catatan Perbaikan pada Kode

Daftar masalah yang ditemukan pada kode awal dan perbaikannya.

| # | Berkas | Masalah | Perbaikan |
|---|---|---|---|
| 1 | `buku/list.html` | Struktur HTML rusak: `<div class="search-box">` menampung tombol dan loading indicator, dan `id="loading-indicator"` muncul **dua kali** | Struktur dirapikan; ID hanya satu |
| 2 | `app.js`, `buku.js`, `anggota.js` | Setelah **Muat Ulang**, semua baris tampil kembali padahal kotak pencarian masih terisi | Filter dipisah menjadi `terapkanFilter()` dan dijalankan ulang setelah data dirender |
| 3 | `app.js` | Pencarian memakai `keyup`, sehingga teks hasil paste tidak terfilter | Diganti ke event `input` |
| 4 | `buku.js` | `colspan="5"` padahal tabel buku punya 6 kolom | Diubah menjadi `colspan="6"` |
| 5 | Semua halaman | Menu navigasi tidak seragam (link Tambah Buku / Tambah Anggota hilang di sebagian halaman) | Menu disamakan di seluruh halaman |
| 6 | `buku.js`, `anggota.js` | Pesan "Failed to fetch" tidak menjelaskan penyebab | Pesan khusus saat halaman dibuka lewat `file://` |

---

## 13. Rangkuman

| Bagian | Konsep |
|---|---|
| [Konsep Dasar](#4-konsep-dasar) | AJAX, JSON, `fetch()`, Promise, `async`/`await`, `try`/`catch`/`finally` |
| [Perubahan HTML](#5-apa-yang-berubah-di-file-html) | `<tbody>` kosong, loading indicator, urutan `<script>` |
| [Data JSON](#6-data-json) | Struktur `buku.json` dan `anggota.json`, tipe data JSON |
| [Fetch & Render Buku](#7-js-mengambil-dan-menampilkan-daftar-buku) | Alur: fetch → cek status → parse → render → error handling |
| [Fetch & Render Anggota](#8-js-mengambil-dan-menampilkan-daftar-anggota) | Pola yang sama untuk data berbeda |
| [Event Delegation](#9-js-event-delegation-pada-tombol-hapus) | Menangani elemen yang dibuat dinamis |
| [Filter Tabel](#10-js-pencarian-tabel-yang-bekerja-dengan-data-dinamis) | Filter yang dijalankan ulang setelah render |
| [Server Lokal & CORS](#11-server-lokal-dan-cors) | Kenapa `fetch()` butuh `http://` |

### Lima konsep inti

1. **`fetch()` selalu asinkron.** Hasilnya Promise; butuh `await` di dalam `async function` untuk mendapat data sungguhan.
2. **`res.ok` harus diperiksa manual.** `fetch()` tidak otomatis gagal hanya karena file tidak ditemukan.
3. **`try`/`catch`/`finally` menjaga aplikasi tetap "hidup".** Pengguna melihat pesan error yang jelas, bukan halaman kosong tanpa penjelasan.
4. **Event delegation diperlukan untuk elemen dinamis.** Pasang listener di leluhur yang stabil, bukan di elemen yang dibuat belakangan.
5. **`fetch()` butuh server, bukan `file://`.** Selalu jalankan lewat server lokal.

### Cara mencoba sendiri

1. Jalankan server lokal ([bab 2](#2-cara-menjalankan)).
2. Buka `http://localhost:8000/buku/list.html`. Amati "Memuat data..." muncul sekilas sebelum tabel terisi 10 baris.
3. Buka **DevTools → tab Network**, refresh, cari permintaan `buku.json`, lalu bandingkan respons mentahnya dengan `data/buku.json`.
4. Coba pencarian, tombol Hapus, dan tombol **Muat Ulang**; semuanya tetap berfungsi meski baris dibuat dinamis.
5. Praktikkan uji error di [11.5](#115-cara-menguji-penanganan-error).

---

## 14. Latihan Lanjutan

| # | Latihan | Status di kode ini |
|---|---|---|
| 1 | Tambah tombol **Muat Ulang** yang memanggil ulang `muatDaftarBuku()` | **Sudah ada** di Daftar Buku. Coba tambahkan juga di Daftar Anggota |
| 2 | Satukan `buku.js` dan `anggota.js` menjadi satu fungsi generik yang menerima nama file JSON dan daftar kunci | Belum |
| 3 | Tambah kolom baru di JSON (mis. `kategori`), lalu tampilkan di tabel dengan menambah `<th>` dan `<td>` | **Sudah ada** untuk `kategori`. Coba kolom lain, mis. `isbn` |
| 4 | Tambahkan `console.log(e.target)` di `initHapusConfirm`, lalu klik berbagai tempat untuk melihat bagaimana `document` menerima **semua** klik | Belum |
| 5 | Ubah delay simulasi dari `600` menjadi `3000` untuk merasakan pentingnya loading indicator pada koneksi lambat | Belum |
| 6 | Hitung angka **Ringkasan** di Beranda dari `buku.json` dan `anggota.json` (kini masih teks statis) | Belum |
| 7 | Simpan data dari form tambah ke `localStorage`, lalu gabungkan dengan data JSON saat menampilkan daftar | Belum |

**Tips:** ketik langsung di Console (dari `buku/list.html` yang dibuka lewat server lokal) untuk melihat data tanpa lewat tabel:

```js
await fetch("../data/buku.json").then(r => r.json())
```

---

## 15. Pemecahan Masalah

| Gejala | Penyebab | Solusi |
|---|---|---|
| "Gagal memuat data: ... file://..." atau "Failed to fetch" | Dibuka lewat `file://` | Jalankan lewat server lokal ([bab 2](#2-cara-menjalankan)) |
| "Gagal memuat data (status 404)" | Path atau nama file JSON salah | Pastikan `data/buku.json` dan `data/anggota.json` ada; periksa path `../data/...` |
| "Unexpected token ... in JSON" | JSON tidak valid (koma berlebih/kurang, kunci tanpa kutip, ada komentar) | Validasi di jsonlint.com |
| Sel tabel kosong atau bertuliskan "undefined" | Nama kunci di kode tidak sama dengan di JSON | Samakan persis, termasuk huruf besar-kecil dan garis bawah |
| "Memuat data..." tidak pernah hilang | `finally` hilang, atau script lain error sebelum `finally` | Cek Console (`F12`) |
| Tombol Hapus tidak bereaksi | Listener masih memakai versi lama (`querySelectorAll`) | Pakai event delegation ([bab 9](#9-js-event-delegation-pada-tombol-hapus)) |
| Tabel kosong tanpa error | JSON berupa array kosong, atau kotak pencarian masih berisi kata kunci | Cek isi JSON; kosongkan kotak pencarian |
| Perubahan CSS/JS tidak terlihat | Cache browser | Hard refresh (`Ctrl + Shift + R`) |
| Menu ☰ tidak muncul | Lebar layar > 480 px | Perkecil jendela browser |
| Form disubmit lalu halaman hanya memuat ulang | Form belum terhubung ke penyimpanan (memang belum di jobsheet ini) | Lihat latihan 7 |

Untuk melihat error teknis, buka **DevTools** (`F12`), lalu periksa tab **Console** dan **Network**.

---

*SIMPUS-Mini, Jobsheet 6.*
