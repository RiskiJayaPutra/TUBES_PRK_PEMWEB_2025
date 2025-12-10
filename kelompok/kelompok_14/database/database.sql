-- Buat database
CREATE DATABASE IF NOT EXISTS fixtrack;
USE fixtrack;

-- Tabel user (admin & teknisi)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin', 'teknisi') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel servis (transaksi)
CREATE TABLE IF NOT EXISTS servis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_resi VARCHAR(20) NOT NULL UNIQUE,
    tgl_masuk DATETIME DEFAULT CURRENT_TIMESTAMP,
    tgl_mulai DATETIME NULL,
    tgl_selesai DATETIME NULL,
    tgl_keluar DATETIME NULL,
    id_teknisi INT NULL,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    kelengkapan TEXT NOT NULL,
    keluhan_awal TEXT NOT NULL,
    kerusakan_fix TEXT NULL,
    estimasi_hari INT NULL,
    biaya DECIMAL(10, 2) NULL,
    status ENUM('Barang Masuk', 'Pengecekan', 'Menunggu Sparepart', 'Pengerjaan', 'Selesai', 'Batal', 'Diambil') DEFAULT 'Barang Masuk',
    FOREIGN KEY (id_teknisi) REFERENCES users(id) ON DELETE SET NULL
);

-- Akun default (password: admin123 / teknisi123)
INSERT INTO users (username, password, nama, role) VALUES 
('admin', 'admin123', 'Administrator', 'admin'),
('teknisi', 'teknisi123', 'Teknisi 1', 'teknisi');

-- Tabel rincian biaya servis
CREATE TABLE IF NOT EXISTS biaya_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_servis INT NOT NULL,
    nama_item VARCHAR(100) NOT NULL,
    harga DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_servis) REFERENCES servis(id) ON DELETE CASCADE
);
