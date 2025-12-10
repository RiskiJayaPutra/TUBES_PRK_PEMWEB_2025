<?php
session_start();
require_once "../config.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: superadmin.php");
exit;
?>
