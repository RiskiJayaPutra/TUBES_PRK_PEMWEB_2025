<?php
session_start();
include '../config.php';

// Dukung kedua nama session key untuk kompatibilitas
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id'] ?? $_SESSION['id_user']);

// Ambil data user dengan prepared statement
$stmt = $conn->prepare("SELECT id, username, nama, role, foto, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows <= 0) {
    header("Location: ../login.php");
    exit();
}
$user = $res->fetch_assoc();
$stmt->close();

// Tentukan link dashboard berdasarkan role (relatif dari src/profile)
if (isset($user['role']) && $user['role'] === 'admin') {
    $dashboard_link = '../halaman-admin/index.php';
} else {
    $dashboard_link = '../halaman-teknisi/index.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profil Saya - FixTrack</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen font-sans">

    <!-- HEADER (sama gaya dengan header halaman-admin) -->
    <header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="<?= $dashboard_link; ?>" class="flex items-center gap-3">
                        <img src="../assets/photos/logo.png" alt="FixTrack" class="h-10 w-10 object-contain">
                        <span class="font-semibold text-lg text-slate-800">FixTrack</span>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <!-- greeting -->
                    <div class="text-sm text-slate-600 hidden sm:block">
                        Halo, <?= htmlspecialchars($user['nama']); ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="max-w-4xl mx-auto px-4 py-10">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
            <div class="h-32 bg-gradient-to-r from-blue-800 to-blue-600 relative">
                <div class="absolute inset-0 bg-black opacity-10"></div>
            </div>

            <div class="px-8 pb-8">
                <div class="relative flex flex-col sm:flex-row justify-between items-end -mt-12 mb-8">
                    <div class="flex items-end">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full border-4 border-white shadow-md bg-white flex items-center justify-center text-4xl font-bold text-blue-800 overflow-hidden">
                                <?php if (!empty($user['foto'])): ?>
                                    <img src="../assets/photos/<?= htmlspecialchars($user['foto']); ?>" alt="Foto" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="ml-0 sm:ml-6 mt-4 sm:mt-0 mb-2 text-center sm:text-left">
                            <h1 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars($user['nama']); ?></h1>
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide
                                <?= ($user['role'] === 'admin') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                <?= ucfirst($user['role']); ?> System
                            </span>
                        </div>
                    </div>

                    <div class="mb-2 hidden sm:block">
                        <a href="edit_profile.php" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 hover:text-blue-800 hover:border-blue-800 transition ease-in-out duration-150">
                            <i class="fas fa-edit mr-2"></i> Edit Profil
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 mt-4 border-t border-slate-100 pt-8">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Username Login</h3>
                            <div class="flex items-center text-slate-800 font-semibold text-lg p-2 bg-slate-50 rounded-lg">
                                <i class="fas fa-user-circle text-slate-400 mr-3 text-xl"></i>
                                <?= htmlspecialchars($user['username']); ?>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</h3>
                            <div class="flex items-center text-slate-800 font-semibold text-lg p-2 bg-slate-50 rounded-lg">
                                <i class="fas fa-id-card text-slate-400 mr-3 text-xl"></i>
                                <?= htmlspecialchars($user['nama']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Role / Jabatan</h3>
                            <div class="flex items-center text-slate-800 font-semibold text-lg p-2 bg-slate-50 rounded-lg">
                                <i class="fas fa-briefcase text-slate-400 mr-3 text-xl"></i>
                                <?= ucfirst($user['role']); ?>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Bergabung Sejak</h3>
                            <div class="flex items-center text-slate-800 font-semibold text-lg p-2 bg-slate-50 rounded-lg">
                                <i class="fas fa-calendar-alt text-slate-400 mr-3 text-xl"></i>
                                <?php
                                    if (!empty($user['created_at'])) {
                                        $date = date_create($user['created_at']);
                                        echo date_format($date, "d F Y");
                                    } else {
                                        echo "-";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 sm:hidden">
                    <a href="edit_profile.php" class="block w-full text-center px-4 py-3 bg-blue-800 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg">
                        <i class="fas fa-edit mr-2"></i> Edit Profil
                    </a>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                    <a href="<?= $dashboard_link; ?>" class="text-slate-500 hover:text-blue-800 font-medium flex items-center gap-2 transition group">
                        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>