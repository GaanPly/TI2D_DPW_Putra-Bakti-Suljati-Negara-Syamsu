# Jobsheet 6
Nama: Putra Bakti Suljati Negara Syamsu <br>
Kelas: TI 2D <br>
NIM: 254107020147 <br>


- Pembuatan berkas sumber data statis `data/buku.json` (10 data buku) dan `data/anggota.json` (4 data anggota) sebagai representasi sementara antarmuka API.
- Pemuatan data tabel secara asinkron (`async/await` dan `fetch()`) pada halaman Daftar Buku (`assets/js/buku.js`) dan Daftar Anggota (`assets/js/anggota.js`) dengan mengosongkan `<tbody>` statis.
- Penyediaan indikator pemuatan (*loading indicator*) yang tampil selama proses penarikan data berlangsung dengan simulasi jeda jaringan (*delay* 600ms).
- Penanganan galat jaringan (*error handling*) terstruktur menggunakan blok `try...catch...finally` yang menampilkan pesan galat informatif di dalam tabel saat proses fetch gagal.
- Penerapan pola *Event Delegation* pada tombol Hapus di `assets/js/app.js` (`document.addEventListener("click", ...)`) untuk menangani interaksi pada elemen baris tabel yang dirender secara dinamis setelah dokumen selesai dimuat.
