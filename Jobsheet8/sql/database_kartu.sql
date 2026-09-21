-- Jobsheet 8: skema awal database game_database (PostgreSQL)
-- Jalankan setelah membuat database, misal:
--   createdb game_database
--   psql -d game_database -f sql/database_kartu.sql
-- (Bila tabel belum ada, aplikasi juga membuatnya otomatis saat pertama dibuka.)

CREATE TABLE IF NOT EXISTS senjata (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    deskripsi VARCHAR(255) NOT NULL,
    kerusakan INTEGER NOT NULL DEFAULT 0,
    durabilitas INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS karakter (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    nyawa INTEGER NOT NULL DEFAULT 0,
    perlindungan INTEGER NOT NULL DEFAULT 0
);
