<?php
require "../koneksi.php";

// Ambil semua user
$q_user = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Superadmin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8 min-h-screen">

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-[#001F3F] mb-3">Kelola User</h1>

        <a href="tambah_user.php" 
           class="bg-[#001F3F] text-white px-4 py-2 rounded-lg hover:bg-[#003366]">
            + Tambah User
        </a>

        <!-- TABEL -->
        <div class="bg-white p-6 rounded-2xl shadow-lg mt-6">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-3">ID</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Username</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($u = mysqli_fetch_assoc($q_user)) { ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3"><?= $u['id'] ?></td>
                        <td class="p-3"><?= $u['nama'] ?></td>
                        <td class="p-3"><?= $u['username'] ?></td>
                        <td class="p-3"><?= $u['role'] ?></td>
                        <td class="p-3">
                            <a href="edit_user.php?id=<?= $u['id'] ?>" 
                               class="text-blue-600 font-semibold">Edit</a> |
                            <a href="hapus_user.php?id=<?= $u['id'] ?>" 
                               onclick="return confirm('Hapus user?')"
                               class="text-red-600 font-semibold">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <a href="superadmin_dashboard.php"
           class="mt-6 inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Buka Dashboard Superadmin
        </a>

    </div>

</body>
</html>
