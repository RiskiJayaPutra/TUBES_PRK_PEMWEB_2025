<?php
require "../koneksi.php";

$id = $_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
$data = mysqli_fetch_assoc($q);

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    mysqli_query($conn, "UPDATE users SET 
        nama='$nama', 
        username='$username',
        role='$role'
        WHERE id=$id");

    header("Location: superadmin.php");
    exit;
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
