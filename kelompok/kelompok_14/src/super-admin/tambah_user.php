<?php
session_start();
require_once "../config.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash Password!
    $role = $_POST['role'];

    // Gunakan Prepared Statement untuk keamanan
    $stmt = $conn->prepare("INSERT INTO users(username, password, nama, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $nama, $role);
    
    if ($stmt->execute()) {
        header("Location: superadmin.php");
        exit;
    } else {
        echo "Gagal menambah user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8 min-h-screen">

    <div class="max-w-xl mx-auto bg-white p-6 rounded-2xl shadow-lg">
        <h1 class="text-2xl font-bold text-[#001F3F] mb-4">Tambah User</h1>

        <form method="post">

            <label class="block mb-2 font-semibold">Nama</label>
            <input type="text" name="nama" required
                   class="w-full border p-2 rounded-lg mb-4">

            <label class="block mb-2 font-semibold">Username</label>
            <input type="text" name="username" required
                   class="w-full border p-2 rounded-lg mb-4">

            <label class="block mb-2 font-semibold">Password</label>
            <input type="text" name="password" required
                   class="w-full border p-2 rounded-lg mb-4">

            <label class="block mb-2 font-semibold">Role</label>
            <select name="role" class="w-full border p-2 rounded-lg mb-4">
                <option value="admin">Admin</option>
                <option value="teknisi">Teknisi</option>
                <option value="superadmin">Superadmin</option>
            </select>

            <button name="submit"
                    class="bg-[#001F3F] text-white px-4 py-2 rounded-lg hover:bg-[#003466]">
                Simpan
            </button>

            <a href="superadmin.php" class="ml-4 text-gray-500">Kembali</a>

        </form>

    </div>

</body>
</html>
