<?php
session_start();
require_once '../config.php';

// Cek login & role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data servis
$stmt = $conn->prepare("SELECT servis.*, users.nama as nama_teknisi FROM servis LEFT JOIN users ON servis.id_teknisi = users.id WHERE servis.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: list_servis.php");
    exit();
}

$servis = $result->fetch_assoc();

// Ambil daftar teknisi
$teknisi_result = $conn->query("SELECT id, nama FROM users WHERE role = 'teknisi'");

$success_msg = '';
$error_msg = '';

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $id_teknisi = $_POST['id_teknisi'] ?: null;
    $kerusakan_fix = $_POST['kerusakan_fix'];
    // Hapus titik dari format rupiah sebelum konversi
    $biaya = $_POST['biaya'] ? floatval(str_replace('.', '', $_POST['biaya'])) : null;
    $tgl_mulai = $_POST['tgl_mulai'] ?: null;
    $tgl_selesai = $_POST['tgl_selesai'] ?: null;
    $tgl_keluar = $_POST['tgl_keluar'] ?: null;

    $stmt = $conn->prepare("UPDATE servis SET status = ?, id_teknisi = ?, kerusakan_fix = ?, biaya = ?, tgl_mulai = ?, tgl_selesai = ?, tgl_keluar = ? WHERE id = ?");
    $stmt->bind_param("sisssssi", $status, $id_teknisi, $kerusakan_fix, $biaya, $tgl_mulai, $tgl_selesai, $tgl_keluar, $id);

    if ($stmt->execute()) {
        $success_msg = "Data servis berhasil diupdate!";
        // Refresh data
        $stmt = $conn->prepare("SELECT servis.*, users.nama as nama_teknisi FROM servis LEFT JOIN users ON servis.id_teknisi = users.id WHERE servis.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $servis = $stmt->get_result()->fetch_assoc();
    } else {
        $error_msg = "Gagal update: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Servis - FixTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <a href="list_servis.php" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Servis</h1>
                <p class="text-slate-500 text-sm">No. Resi: <span class="font-semibold text-blue-600"><?php echo $servis['no_resi']; ?></span></p>
            </div>
        </div>

        <?php if ($success_msg): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r">
                <p class="font-medium"><?php echo $success_msg; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r">
                <p><?php echo $error_msg; ?></p>
            </div>
        <?php endif; ?>

        <!-- Info Pelanggan (Read Only) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
            <h3 class="text-sm font-semibold text-slate-500 uppercase mb-4">Informasi Pelanggan & Barang</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-slate-400">Pelanggan</span>
                    <p class="font-medium text-slate-700"><?php echo $servis['nama_pelanggan']; ?></p>
                </div>
                <div>
                    <span class="text-slate-400">No. HP</span>
                    <p class="font-medium text-slate-700"><?php echo $servis['no_hp']; ?></p>
                </div>
                <div>
                    <span class="text-slate-400">Barang</span>
                    <p class="font-medium text-slate-700"><?php echo $servis['nama_barang']; ?></p>
                </div>
                <div>
                    <span class="text-slate-400">Tgl Diterima</span>
                    <p class="font-medium text-slate-700"><?php echo date('d M Y', strtotime($servis['tgl_masuk'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Form Edit -->
        <form action="" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-bold text-slate-700 border-b pb-2">
                    <i class="fas fa-edit mr-2 text-blue-500"></i> Edit Data Servis
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                        <select name="status" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Barang Masuk" <?php echo $servis['status'] == 'Barang Masuk' ? 'selected' : ''; ?>>Barang Masuk</option>
                            <option value="Pengecekan" <?php echo $servis['status'] == 'Pengecekan' ? 'selected' : ''; ?>>Pengecekan</option>
                            <option value="Menunggu Sparepart" <?php echo $servis['status'] == 'Menunggu Sparepart' ? 'selected' : ''; ?>>Menunggu Sparepart</option>
                            <option value="Pengerjaan" <?php echo $servis['status'] == 'Pengerjaan' ? 'selected' : ''; ?>>Pengerjaan</option>
                            <option value="Selesai" <?php echo $servis['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                            <option value="Diambil" <?php echo $servis['status'] == 'Diambil' ? 'selected' : ''; ?>>Diambil</option>
                            <option value="Batal" <?php echo $servis['status'] == 'Batal' ? 'selected' : ''; ?>>Batal</option>
                        </select>
                    </div>

                    <!-- Teknisi -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Teknisi</label>
                        <select name="id_teknisi" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Teknisi --</option>
                            <?php 
                            mysqli_data_seek($teknisi_result, 0);
                            while($tek = $teknisi_result->fetch_assoc()): ?>
                                <option value="<?php echo $tek['id']; ?>" <?php echo $servis['id_teknisi'] == $tek['id'] ? 'selected' : ''; ?>><?php echo $tek['nama']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Riwayat Tanggal</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Mulai Dikerjakan</label>
                            <input type="date" name="tgl_mulai" value="<?php echo $servis['tgl_mulai'] ? date('Y-m-d', strtotime($servis['tgl_mulai'])) : ''; ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Selesai Dikerjakan</label>
                            <input type="date" name="tgl_selesai" value="<?php echo $servis['tgl_selesai'] ? date('Y-m-d', strtotime($servis['tgl_selesai'])) : ''; ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Diambil Pemilik</label>
                            <input type="date" name="tgl_keluar" value="<?php echo $servis['tgl_keluar'] ? date('Y-m-d', strtotime($servis['tgl_keluar'])) : ''; ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Kerusakan Fix -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kerusakan yang Diperbaiki</label>
                    <textarea name="kerusakan_fix" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Jelaskan kerusakan yang sudah diperbaiki..."><?php echo htmlspecialchars($servis['kerusakan_fix'] ?? ''); ?></textarea>
                </div>

                <!-- Biaya -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Biaya Servis (Rp)</label>
                    <input type="text" name="biaya" value="<?php echo $servis['biaya'] ? number_format($servis['biaya'], 0, ',', '.') : ''; ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: 150.000" oninput="formatRupiah(this)">
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end gap-4">
                <a href="list_servis.php" class="px-6 py-3 rounded-lg font-medium text-slate-600 hover:bg-white transition-colors border border-slate-200">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg shadow-blue-200 transition-all">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>

<script>
    function formatRupiah(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            input.value = parseInt(value).toLocaleString('id-ID');
        }
    }
</script>

</body>
</html>
