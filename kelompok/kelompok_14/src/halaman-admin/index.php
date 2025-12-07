<?php
require_once '../config.php';

// --- Ambil Statistik ---
// 1. Total Barang Masuk (Barang Masuk + Pengecekan + Menunggu Sparepart)
$sql_masuk = "SELECT COUNT(*) as total FROM servis WHERE status IN ('Barang Masuk', 'Pengecekan', 'Menunggu Sparepart')";
$total_masuk = $conn->query($sql_masuk)->fetch_assoc()['total'];

// 2. Sedang Dikerjakan (Pengerjaan)
$sql_proses = "SELECT COUNT(*) as total FROM servis WHERE status = 'Pengerjaan'";
$total_proses = $conn->query($sql_proses)->fetch_assoc()['total'];

// 3. Siap Diambil (Selesai - belum Diambil)
$sql_selesai = "SELECT COUNT(*) as total FROM servis WHERE status = 'Selesai'";
$total_selesai = $conn->query($sql_selesai)->fetch_assoc()['total'];

// 4. Omset Bulan Ini (Status Selesai/Diambil, tgl_keluar bulan ini)
// Asumsi: biaya dihitung saat status Selesai atau Diambil.
// Menggunakan tgl_masuk sebagai filter bulan ini untuk simplifikasi jika tgl_keluar belum ada
$current_month = date('m');
$current_year = date('Y');
$sql_omset = "SELECT SUM(biaya) as total FROM servis WHERE (status = 'Selesai' OR status = 'Diambil') AND MONTH(tgl_masuk) = '$current_month' AND YEAR(tgl_masuk) = '$current_year'";
$omset = $conn->query($sql_omset)->fetch_assoc()['total'];
$omset_formatted = "Rp " . number_format($omset ?? 0, 0, ',', '.');

// --- Ambil Data Terbaru (Limit 5) ---
$sql_recent = "SELECT servis.*, users.nama as nama_teknisi 
               FROM servis 
               LEFT JOIN users ON servis.id_teknisi = users.id 
               ORDER BY tgl_masuk DESC LIMIT 5";
$recent_result = $conn->query($sql_recent);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FixTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <!-- Navbar -->
<nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shadow-sm sticky top-0 z-30">
    <div class="flex items-center gap-3">
        <div class="bg-blue-600 text-white p-2 rounded-lg">
            <i class="fas fa-tools"></i>
        </div>
        <h1 class="text-xl font-bold text-slate-800">
            FixTrack <span class="text-blue-600">Admin</span>
        </h1>
    </div>

    <div class="flex items-center gap-4">
        <span class="text-slate-600 text-sm font-medium">Halo, Admin</span>

        <!-- Perbaikan link ke profile.php -->
        <a href="../profile/profile.php" class="text-blue-600 hover:text-blue-700 flex items-center gap-1 text-sm font-medium">
            <i class="fas fa-user"></i> Profil
        </a>

        <!-- Perbaikan link ke login.php -->
        <a href="../login.php" class="text-red-600 hover:text-red-700 flex items-center gap-1 text-sm font-medium">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>

    <div class="container mx-auto px-6 py-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Dashboard</h2>
                <p class="text-slate-500 mt-1">Ringkasan aktivitas bengkel hari ini</p>
            </div>
            <a href="tambah_servis.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-blue-200 transition-all flex items-center gap-2 transform hover:-translate-y-1">
                <i class="fas fa-plus"></i> Input Servis Baru
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Card 1 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-50 text-blue-600 p-3 rounded-xl">
                        <i class="fas fa-box-open text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-400">Antrian Baru</span>
                </div>
                <h3 class="text-3xl font-bold text-slate-800"><?php echo $total_masuk; ?></h3>
                <p class="text-slate-500 text-sm mt-1">Perlu Diproses</p>
            </div>
            
            <!-- Card 2 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-yellow-50 text-yellow-600 p-3 rounded-xl">
                        <i class="fas fa-wrench text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-400">Proses</span>
                </div>
                <h3 class="text-3xl font-bold text-slate-800"><?php echo $total_proses; ?></h3>
                <p class="text-slate-500 text-sm mt-1">Sedang Dikerjakan</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-50 text-green-600 p-3 rounded-xl">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-400">Selesai</span>
                </div>
                <h3 class="text-3xl font-bold text-slate-800"><?php echo $total_selesai; ?></h3>
                <p class="text-slate-500 text-sm mt-1">Siap Diambil</p>
            </div>

             <!-- Card 4 -->
             <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-50 text-purple-600 p-3 rounded-xl">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-400">Omset</span>
                </div>
                <h3 class="text-3xl font-bold text-slate-800"><?php echo $omset_formatted; ?></h3>
                <p class="text-slate-500 text-sm mt-1">Bulan Ini</p>
            </div>
        </div>

        <!-- Recent Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Servis Terbaru</h3>
                <a href="list_servis.php" class="text-blue-600 text-sm font-medium hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold tracking-wider">
                            <th class="px-6 py-4">No. Resi</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Barang</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Teknisi</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if ($recent_result->num_rows > 0): ?>
                            <?php while($row = $recent_result->fetch_assoc()): ?>
                                <?php
                                    // Warna Badge Status
                                    $status_class = 'bg-slate-100 text-slate-700';
                                    if ($row['status'] == 'Barang Masuk') $status_class = 'bg-blue-100 text-blue-700';
                                    elseif ($row['status'] == 'Pengerjaan') $status_class = 'bg-yellow-100 text-yellow-700';
                                    elseif ($row['status'] == 'Selesai') $status_class = 'bg-green-100 text-green-700';
                                    elseif ($row['status'] == 'Diambil') $status_class = 'bg-gray-100 text-gray-700 line-through';
                                    elseif ($row['status'] == 'Batal') $status_class = 'bg-red-100 text-red-700';
                                ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-800"><?php echo $row['no_resi']; ?></td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <div class="font-medium"><?php echo $row['nama_pelanggan']; ?></div>
                                        <div class="text-xs text-slate-400"><?php echo $row['no_hp']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600"><?php echo $row['nama_barang']; ?></td>
                                    <td class="px-6 py-4">
                                        <span class="<?php echo $status_class; ?> px-3 py-1 rounded-full text-xs font-semibold">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 text-sm">
                                        <?php echo $row['nama_teknisi'] ? $row['nama_teknisi'] : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="detail_servis.php?id=<?php echo $row['id']; ?>" class="text-slate-400 hover:text-blue-600 transition-colors bg-slate-100 hover:bg-blue-50 p-2 rounded-lg">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data servis.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
