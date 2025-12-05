-- Database: fixtrack

CREATE DATABASE IF NOT EXISTS fixtrack;
USE fixtrack;

-- Table: users
-- Stores admin and technician accounts
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin', 'teknisi') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: servis
-- Stores service transactions and tracking info
CREATE TABLE IF NOT EXISTS servis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_resi VARCHAR(20) NOT NULL UNIQUE,
    tgl_masuk DATETIME DEFAULT CURRENT_TIMESTAMP,
    tgl_keluar DATETIME NULL,
    id_teknisi INT NULL,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    kelengkapan TEXT NOT NULL,
    keluhan_awal TEXT NOT NULL,
    kerusakan_fix TEXT NULL,
    biaya DECIMAL(10, 2) NULL,
    status ENUM('Barang Masuk', 'Pengecekan', 'Menunggu Sparepart', 'Pengerjaan', 'Selesai', 'Batal', 'Diambil') DEFAULT 'Barang Masuk',
    FOREIGN KEY (id_teknisi) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin for initial access (password: admin123)
-- Note: In production, use password hashing (e.g., password_hash in PHP)
INSERT INTO users (username, password, nama, role) VALUES 
('admin', 'admin123', 'Administrator', 'admin'),
('teknisi', 'teknisi123', 'Teknisi 1', 'teknisi');
