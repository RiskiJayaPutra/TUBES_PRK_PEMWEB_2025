<?php
session_start();
require_once "../config.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo "User tidak ditemukan!";
    exit;
}

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Optional: Update password logic could be added here if requested, but for now just profile.
    $stmt_upd = $conn->prepare("UPDATE users SET nama = ?, username = ?, role = ? WHERE id = ?");
    $stmt_upd->bind_param("sssi", $nama, $username, $role, $id);

    if ($stmt_upd->execute()) {
        header("Location: superadmin.php");
        exit;
    } else {
        echo "Gagal update user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8 min-h-screen">

    <div class="max-w-xl mx-auto bg-white p-6 rounded-2xl shadow-lg">

        <h1 class="text-2xl font-bold text-[#001F3F] mb-4">Edit User</h1>

        <form method="post">

            <label class="block mb-2 font-semibold">Nama</label>
            <input type="text" name="nama" value="<?= $data['nama'] ?>" required
                   class="w-full border p-2 rounded-lg mb-4">

            <label class="block mb-2 font-semibold">Username</label>
            <input type="text" name="username" value="<?= $data['username'] ?>" required
                   class="w-full border p-2 rounded-lg mb-4">

            <label class="block mb-2 font-semibold">Role</label>
            <select name="role" class="w-full border p-2 rounded-lg mb-4">
                <option value="admin" <?= $data['role']=='admin'?'selected':'' ?>>Admin</option>
                <option value="teknisi" <?= $data['role']=='teknisi'?'selected':'' ?>>Teknisi</option>
                <option value="superadmin" <?= $data['role']=='superadmin'?'selected':'' ?>>Superadmin</option>
            </select>

            <button name="submit"
                    class="bg-[#001F3F] text-white px-4 py-2 rounded-lg hover:bg-[#003466]">
                Simpan Perubahan
            </button>

            <a href="superadmin.php" class="ml-4 text-gray-500">Kembali</a>

        </form>

    </div>

</body>
</html>
