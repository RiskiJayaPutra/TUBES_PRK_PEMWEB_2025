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
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="list_servis.php" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Edit Servis</h1>
                    <p class="text-slate-500 text-sm">No. Resi: <span class="font-semibold text-blue-600"><?php echo $servis['no_resi']; ?></span>
                        <span class="ml-2 px-2 py-1 text-xs rounded-full font-semibold
                        <?php 
                            if ($servis['status'] == 'Diambil') echo 'bg-purple-100 text-purple-700';
                            elseif ($servis['status'] == 'Selesai') echo 'bg-green-100 text-green-700';
                            elseif ($servis['status'] == 'Pengerjaan') echo 'bg-yellow-100 text-yellow-700';
                            elseif ($servis['status'] == 'Batal') echo 'bg-red-100 text-red-700';
                            else echo 'bg-gray-100 text-gray-700';
                        ?>"><?php echo $servis['status']; ?></span>
                    </p>
                </div>
            </div>
            <a href="print_resi.php?id=<?php echo $id; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-print mr-1"></i> Cetak Resi
            </a>
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
                    <!-- Status (Read Only) -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                        <div class="px-4 py-3 bg-slate-100 border border-slate-200 rounded-lg font-medium
                            <?php 
                                if ($servis['status'] == 'Diambil') echo 'text-purple-700';
                                elseif ($servis['status'] == 'Selesai') echo 'text-green-700';
                                elseif ($servis['status'] == 'Batal') echo 'text-red-700';
                                else echo 'text-slate-700';
                            ?>">
                            <?php 
                                if ($servis['status'] == 'Diambil') echo 'Sudah Diambil Pemilik';
                                elseif ($servis['status'] == 'Selesai') echo 'Selesai (Siap Diambil)';
                                elseif ($servis['status'] == 'Batal') echo 'Dibatalkan';
                                else echo $servis['status'];
                            ?>
                        </div>
                        <input type="hidden" name="status" value="<?php echo $servis['status']; ?>">
                        <p class="text-xs text-slate-400 mt-1">* Perubahan status dilakukan oleh Teknisi</p>
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
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-between items-center">
                <div>
                    <?php if ($servis['status'] == 'Selesai' || $servis['status'] == 'Batal'): ?>
                        <a href="konfirmasi_diambil.php?id=<?php echo $id; ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg shadow-purple-200 transition-all" onclick="return confirm('Konfirmasi barang sudah diambil oleh pemilik?')">
                            <i class="fas fa-check-circle mr-2"></i> Konfirmasi Sudah Diambil
                        </a>
                    <?php elseif ($servis['status'] == 'Diambil'): ?>
                        <span class="text-purple-600 font-medium"><i class="fas fa-check-circle mr-1"></i> Barang sudah diambil pemilik</span>
                    <?php else: ?>
                        <span class="text-slate-400 text-sm">Menunggu teknisi menyelesaikan pengerjaan...</span>
                    <?php endif; ?>
                </div>
                <div class="flex gap-4">
                    <a href="list_servis.php" class="px-6 py-3 rounded-lg font-medium text-slate-600 hover:bg-white transition-colors border border-slate-200">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg shadow-blue-200 transition-all">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
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
