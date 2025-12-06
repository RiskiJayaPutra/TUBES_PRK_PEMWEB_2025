<?php
// Konfigurasi Database
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password kosong
$db   = 'fixtrack';

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
