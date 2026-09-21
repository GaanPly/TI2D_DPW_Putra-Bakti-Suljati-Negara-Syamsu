-- Jobsheet 8: skema awal database simpus_mini (PostgreSQL)
-- Jalankan setelah membuat database, misal:
--   createdb simpus_mini
--   psql -d simpus_mini -f sql/01_buku_anggota.sql

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
