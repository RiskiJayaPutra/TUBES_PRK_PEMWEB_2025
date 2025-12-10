<?php
session_start();
require_once '../config.php';

// Cek login & role teknisi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header("Location: ../login.php");
    exit();
}

$teknisi_id = $_SESSION['user_id'];
$teknisi_nama = $_SESSION['nama'] ?? 'Teknisi';

// Proses update status via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $servis_id = intval($_POST['servis_id']);
    $new_status = $_POST['new_status'];
    
    // Ambil data servis untuk cek tanggal
    $check = $conn->query("SELECT tgl_mulai, tgl_selesai FROM servis WHERE id = $servis_id AND id_teknisi = $teknisi_id")->fetch_assoc();
    
    $tgl_mulai = $check['tgl_mulai'];
    $tgl_selesai = $check['tgl_selesai'];
    
    // Auto set tanggal mulai jika status = Pengerjaan
    if ($new_status == 'Pengerjaan' && !$tgl_mulai) {
        $tgl_mulai = date('Y-m-d H:i:s');
    }
    
    // Auto set tanggal selesai jika status = Selesai
    if ($new_status == 'Selesai' && !$tgl_selesai) {
        $tgl_selesai = date('Y-m-d H:i:s');
    }
    
    $stmt = $conn->prepare("UPDATE servis SET status = ?, tgl_mulai = ?, tgl_selesai = ? WHERE id = ? AND id_teknisi = ?");
    $stmt->bind_param("sssii", $new_status, $tgl_mulai, $tgl_selesai, $servis_id, $teknisi_id);
    $stmt->execute();
    
    header("Location: index.php?updated=1");
    exit();
}

// Statistik
$antrian = $conn->query("SELECT COUNT(*) as total FROM servis WHERE id_teknisi = $teknisi_id AND status IN ('Barang Masuk', 'Pengecekan')")->fetch_assoc()['total'];
$proses = $conn->query("SELECT COUNT(*) as total FROM servis WHERE id_teknisi = $teknisi_id AND status IN ('Pengerjaan', 'Menunggu Sparepart')")->fetch_assoc()['total'];
$selesai = $conn->query("SELECT COUNT(*) as total FROM servis WHERE id_teknisi = $teknisi_id AND status = 'Selesai'")->fetch_assoc()['total'];
$omset = $conn->query("SELECT COALESCE(SUM(biaya), 0) as total FROM servis WHERE id_teknisi = $teknisi_id AND status IN ('Selesai', 'Diambil') AND MONTH(tgl_masuk) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];

// Daftar servis untuk teknisi ini
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT * FROM servis WHERE id_teknisi = $teknisi_id";
if ($search) {
    $query .= " AND (nama_pelanggan LIKE '%$search%' OR no_resi LIKE '%$search%' OR nama_barang LIKE '%$search%')";
}
if ($status_filter) {
    $query .= " AND status = '$status_filter'";
}
$query .= " ORDER BY tgl_masuk DESC";
$servis_list = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Teknisi - FixTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">

    <!-- Header -->
    <header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="../assets/photos/logo.png" alt="FixTrack" class="h-12 w-12 object-contain">
                <h1 class="text-xl font-bold text-gray-800">FixTrack <span class="text-green-600 font-normal">Teknisi</span></h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-gray-600 text-sm">Halo, <?php echo htmlspecialchars($teknisi_nama); ?></span>
                <a href="../profile/profile.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    <i class="fas fa-user mr-1"></i> Profil
                </a>
                <a href="logout.php" class="text-red-600 hover:text-red-700 text-sm font-medium">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6 space-y-6">
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r">
                <p class="font-medium">Status berhasil diupdate!</p>
            </div>
        <?php endif; ?>
        
        <!-- Page Title -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar servis yang ditugaskan kepada Anda</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Antrian</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $antrian; ?></p>
                        <p class="text-xs text-gray-400 mt-1">Perlu Diproses</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Proses</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $proses; ?></p>
                        <p class="text-xs text-gray-400 mt-1">Sedang Dikerjakan</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tools text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Selesai</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $selesai; ?></p>
                        <p class="text-xs text-gray-400 mt-1">Siap Diambil</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Omset</p>
                        <p class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($omset, 0, ',', '.'); ?></p>
                        <p class="text-xs text-gray-400 mt-1">Bulan Ini</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servis Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Daftar Servis Saya</h3>
            </div>

            <!-- Search & Filter -->
            <div class="px-6 py-4 border-b border-gray-100">
                <form action="" method="GET" class="flex flex-wrap gap-3">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                        placeholder="Cari pelanggan / resi / barang..." 
                        class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm flex-1"
                        onchange="this.form.submit()">
                    <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none text-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Pengecekan" <?php echo $status_filter == 'Pengecekan' ? 'selected' : ''; ?>>Pengecekan</option>
                        <option value="Menunggu Sparepart" <?php echo $status_filter == 'Menunggu Sparepart' ? 'selected' : ''; ?>>Menunggu Sparepart</option>
                        <option value="Pengerjaan" <?php echo $status_filter == 'Pengerjaan' ? 'selected' : ''; ?>>Pengerjaan</option>
                        <option value="Selesai" <?php echo $status_filter == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="Batal" <?php echo $status_filter == 'Batal' ? 'selected' : ''; ?>>Batal</option>
                    </select>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No. Resi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Biaya</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if ($servis_list && $servis_list->num_rows > 0): ?>
                            <?php while($row = $servis_list->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-800"><?php echo $row['no_resi']; ?></div>
                                        <div class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($row['tgl_masuk'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                    <td class="px-6 py-4">
                                        <!-- Dropdown Status -->
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="update_status" value="1">
                                            <input type="hidden" name="servis_id" value="<?php echo $row['id']; ?>">
                                            <select name="new_status" onchange="this.form.submit()" 
                                                class="px-3 py-2 text-sm border-2 rounded-lg focus:ring-2 focus:ring-green-500 bg-white cursor-pointer
                                                <?php 
                                                    if ($row['status'] == 'Selesai') echo 'border-green-500 text-green-700';
                                                    elseif ($row['status'] == 'Pengerjaan') echo 'border-yellow-500 text-yellow-700';
                                                    elseif ($row['status'] == 'Pengecekan') echo 'border-indigo-500 text-indigo-700';
                                                    elseif ($row['status'] == 'Menunggu Sparepart') echo 'border-orange-500 text-orange-700';
                                                    elseif ($row['status'] == 'Batal') echo 'border-red-500 text-red-700';
                                                    else echo 'border-gray-300 text-gray-700';
                                                ?>">
                                                <option value="Pengecekan" <?php echo $row['status'] == 'Pengecekan' ? 'selected' : ''; ?>>Pengecekan</option>
                                                <option value="Menunggu Sparepart" <?php echo $row['status'] == 'Menunggu Sparepart' ? 'selected' : ''; ?>>Menunggu Sparepart</option>
                                                <option value="Pengerjaan" <?php echo $row['status'] == 'Pengerjaan' ? 'selected' : ''; ?>>Pengerjaan</option>
                                                <option value="Selesai" <?php echo $row['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                                <option value="Batal" <?php echo $row['status'] == 'Batal' ? 'selected' : ''; ?>>Batal</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                        <?php echo $row['biaya'] ? 'Rp ' . number_format($row['biaya'], 0, ',', '.') : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="update_servis.php?id=<?php echo $row['id']; ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                            <i class="fas fa-edit mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center text-gray-400">
                                        <i class="fas fa-inbox text-4xl mb-3"></i>
                                        <p>Belum ada servis yang ditugaskan kepada Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
