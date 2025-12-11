# RepairinBro - Sistem Manajemen Servis Elektronik

**Kelompok 14 - Praktikum Pemrograman Web**

**Daftar Anggota:**
1. Friskila Rohasina Simarmata (2315061043)
2. Anggi Permata Sari (2315061044)
3. Riski Jaya Putra (2315061065)
4. Intan Eka Safitri (2315061064)

---

## 1. Deskripsi Proyek
**RepairinBro** adalah aplikasi manajemen servis elektronik berbasis web yang dirancang untuk membantu UMKM (Digital Transformation for SMEs) dalam mengelola operasional bengkel reparasi. Sistem ini mencakup alur kerja lengkap mulai dari penerimaan barang, pengerjaan teknisi, hingga pengambilan barang oleh pelanggan, serta fitur pelacakan resi publik.

## 2. Fitur Utama

### Public Tracking
Pelanggan dapat melacak status servis secara real-time menggunakan Nomor Resi tanpa perlu login. Menampilkan status pengerjaan, estimasi waktu, dan rincian biaya.

### Admin Dashboard (Front Office)
*   Form penerimaan barang dengan auto-generate Nomor Resi.
*   Cetak Resi fisik untuk pelanggan.
*   Manajemen antrian servis.
*   Verifikasi pembayaran dan pengambilan barang.

### Teknisi Dashboard (Back Office)
*   Update status pengerjaan (Pengecekan, Pengerjaan, Selesai).
*   Input diagnosa kerusakan dan estimasi biaya.
*   Pencatatan penggunaan sparepart dengan perhitungan otomatis.
*   Riwayat pengerjaan teknisi.

### Superadmin
*   Manajemen pengguna (Admin & Teknisi).
*   Log aktivitas sistem (Audit Trail).
*   Pengaturan profil toko/bengkel.
*   Backup database.

---

## 3. Teknologi yang Digunakan
*   **Backend:** PHP Native (PHP 8.x)
*   **Database:** MySQL / MariaDB
*   **Frontend:** HTML5, CSS3, Tailwind CSS (via CDN)
*   **JavaScript:** Native JS, SweetAlert2

---

## 4. Cara Instalasi dan Menjalankan Aplikasi

1.  **Persiapan Server**
    *   Pastikan komputer telah terinstall XAMPP atau Laragon (PHP versi 8.0 ke atas).
    *   Jalankan modul Apache dan MySQL.

2.  **Setup Database**
    *   Buka phpMyAdmin (http://localhost/phpmyadmin).
    *   Buat database baru dengan nama `fixtrack`.
    *   Import file `database/database.sql` yang terdapat dalam folder root project.

3.  **Konfigurasi Koneksi**
    *   Buka file `src/config.php`.
    *   Pastikan konfigurasi database sesuai dengan server Anda:
        ```php
        $servername = "localhost";
        $user = "root";
        $pass = ""; 
        $db   = "fixtrack";
        ```

4.  **Menjalankan Aplikasi**
    *   Pindahkan folder project ke dalam direktori `htdocs` (misal: `C:\xampp\htdocs\kelompok_14`).
    *   Akses aplikasi melalui browser:
        *   **Landing Page**: `http://localhost/kelompok_14/src/index.php`
        *   **Login Page**: `http://localhost/kelompok_14/src/login.php`

5.  **Akun Default**
    *   **Superadmin**: Username: `superadmin` / Password: `superadmin123`
    *   **Admin**: Username: `admin` / Password: `admin123`
    *   **Teknisi**: Username: `teknisi` / Password: `teknisi123`
