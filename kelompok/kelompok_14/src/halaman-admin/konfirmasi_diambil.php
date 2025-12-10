<?php
session_start();
require_once '../config.php';

// Cek login & role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Update status ke Diambil dan set tanggal keluar
$tgl_keluar = date('Y-m-d H:i:s');
$stmt = $conn->prepare("UPDATE servis SET status = 'Diambil', tgl_keluar = ? WHERE id = ? AND (status = 'Selesai' OR status = 'Batal')");
$stmt->bind_param("si", $tgl_keluar, $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: edit_servis.php?id=$id&success=diambil");
} else {
    header("Location: edit_servis.php?id=$id&error=gagal");
}
exit();
?>
