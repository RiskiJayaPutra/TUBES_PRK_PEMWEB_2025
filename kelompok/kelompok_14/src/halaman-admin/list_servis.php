<?php
require_once '../config.php';

// Fitur Pencarian & Filter
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT servis.*, users.nama as nama_teknisi 
          FROM servis 
          LEFT JOIN users ON servis.id_teknisi = users.id 
          WHERE (nama_pelanggan LIKE '%$search%' OR no_resi LIKE '%$search%' OR nama_barang LIKE '%$search%')";

if ($status_filter) {
    $query .= " AND status = '$status_filter'";
}

$query .= " ORDER BY tgl_masuk DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Servis - FixTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shadow-sm sticky top-0 z-30">
        <div class="flex items-center gap-3">
             <a href="index.php" class="text-slate-500 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Daftar Servis</h1>
        </div>
        <a href="tambah_servis.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i> Baru
        </a>
    </nav>

    <div class="container mx-auto px-6 py-8">
        
        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-6">
            <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 top-3.5 text-slate-400"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Resi / Pelanggan / Barang..." class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full md:w-48">
                    <select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Barang Masuk" <?php echo $status_filter == 'Barang Masuk' ? 'selected' : ''; ?>>Barang Masuk</option>
                        <option value="Pengerjaan" <?php echo $status_filter == 'Pengerjaan' ? 'selected' : ''; ?>>Pengerjaan</option>
                        <option value="Selesai" <?php echo $status_filter == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="Diambil" <?php echo $status_filter == 'Diambil' ? 'selected' : ''; ?>>Diambil</option>
                    </select>
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Cari
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold tracking-wider">
                            <th class="px-6 py-4">Info Resi</th>
                            <th class="px-6 py-4">Barang</th>
                            <th class="px-6 py-4">Estimasi</th>
                            <th class="px-6 py-4">Teknisi</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php
                                    $status_class = 'bg-slate-100 text-slate-700';
                                    if ($row['status'] == 'Barang Masuk') $status_class = 'bg-blue-100 text-blue-700';
                                    elseif ($row['status'] == 'Pengerjaan') $status_class = 'bg-yellow-100 text-yellow-700';
                                    elseif ($row['status'] == 'Selesai') $status_class = 'bg-green-100 text-green-700';
                                    elseif ($row['status'] == 'Diambil') $status_class = 'bg-gray-100 text-gray-700 line-through';
                                ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800"><?php echo $row['no_resi']; ?></div>
                                        <div class="text-sm text-slate-500"><?php echo date('d M Y', strtotime($row['tgl_masuk'])); ?></div>
                                        <div class="text-sm text-blue-600 font-medium mt-1"><?php echo $row['nama_pelanggan']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <div class="font-medium"><?php echo $row['nama_barang']; ?></div>
                                        <div class="text-xs text-slate-400 mt-1 truncate max-w-[200px]" title="<?php echo $row['keluhan_awal']; ?>"><?php echo $row['keluhan_awal']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <div class="flex items-center gap-2">
                                            <i class="far fa-clock text-slate-400"></i>
                                            <?php echo $row['estimasi_hari'] ? $row['estimasi_hari'] . ' Hari' : '-'; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        <?php if ($row['nama_teknisi']): ?>
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">
                                                    <?php echo substr($row['nama_teknisi'], 0, 1); ?>
                                                </div>
                                                <?php echo $row['nama_teknisi']; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-slate-400 italic">Belum assign</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="<?php echo $status_class; ?> px-3 py-1 rounded-full text-xs font-semibold">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <!-- Tombol Aksi Detail/Edit -->
                                            <a href="edit_servis.php?id=<?php echo $row['id']; ?>" class="bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 p-2 rounded-lg transition-all" title="Edit">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($row['status'] == 'Selesai'): ?>
                                                <!-- Tombol Barang Diambil (Hanya muncul jika Selesai) -->
                                                <a href="#" onclick="konfirmasiDiambil(<?php echo $row['id']; ?>)" class="bg-green-600 text-white hover:bg-green-700 p-2 rounded-lg transition-all shadow-sm" title="Barang Diambil">
                                                    <i class="fas fa-check-double"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <i class="fas fa-box-open text-4xl mb-3"></i>
                                        <p>Tidak ada data servis ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination (Placeholder) -->
            <div class="px-6 py-4 border-t border-slate-100 flex justify-center">
                <span class="text-xs text-slate-400">Menampilkan hasil terbaru</span>
            </div>
        </div>
    </div>

<script>
function konfirmasiDiambil(id) {
    Swal.fire({
        title: 'Konfirmasi Pengambilan',
        text: 'Barang sudah diambil pelanggan dan pembayaran lunas?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Sudah Diambil',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'update_status.php?id=' + id + '&status=Diambil';
        }
    });
}
</script>
</body>
</html>
