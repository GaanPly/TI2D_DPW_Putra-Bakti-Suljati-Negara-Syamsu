# Jobsheet 5
Nama: Putra Bakti suljati Negara Syamsu <br>
Kelas: TI 2D <br>
NIM: 254107020147 <br>


## Tugas Mandiri
- Penggantian navigasi hamburger CSS (*checkbox hack*) menjadi interaksi tombol berbasis JavaScript (`classList.toggle("nav-open")`) melalui fungsi `initNavToggle`.
- Fitur pencarian dan penyaringan data secara *real-time* pada tabel Buku dan Anggota menggunakan event `keyup` dan seleksi baris dinamis (`includes()`) melalui fungsi `initTableFilter`.
- Dialog konfirmasi hapus data interaktif menggunakan `confirm()` dan penghapusan baris tabel langsung dari tampilan DOM (`row.remove()`) melalui fungsi `initHapusConfirm`.
- Validasi form di sisi klien (*client-side*) pada form Tambah Buku dan Tambah Anggota yang mencegah pengiriman form kosong/tidak valid (`preventDefault()`) serta memunculkan/menghapus pesan galat inline melalui manipulasi DOM (`createElement`, `insertAdjacentElement`) melalui fungsi `initValidasiForm`.
