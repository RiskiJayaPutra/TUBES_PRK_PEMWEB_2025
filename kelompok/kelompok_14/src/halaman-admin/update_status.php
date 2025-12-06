<?php
require_once '../config.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status']; // Seharusnya 'Diambil'

    if ($status === 'Diambil') {
        // Update status menjadi Diambil DAN set tgl_keluar = sekarang
        $stmt = $conn->prepare("UPDATE servis SET status = ?, tgl_keluar = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            // Berhasil, redirect kembali ke list
            header("Location: list_servis.php?status=success");
        } else {
            echo "Gagal update status: " . $conn->error;
        }
    } else {
        // Untuk update status lain (jika ada logic lain)
        // Saat ini hanya handle Diambil
        header("Location: list_servis.php");
    }
} else {
    header("Location: list_servis.php");
}
?>
