<?php
session_start();
require_once '../config.php';

// Cek login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$teknisi_id = $_SESSION['user_id'];

// Ambil data servis (pastikan milik teknisi ini)
$stmt = $conn->prepare("SELECT * FROM servis WHERE id = ? AND id_teknisi = ?");
$stmt->bind_param("ii", $id, $teknisi_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$servis = $result->fetch_assoc();
$success_msg = '';
$error_msg = '';

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $kerusakan_fix = $_POST['kerusakan_fix'];
    $biaya = $_POST['biaya'] ? floatval($_POST['biaya']) : null;

    $stmt = $conn->prepare("UPDATE servis SET status = ?, kerusakan_fix = ?, biaya = ? WHERE id = ? AND id_teknisi = ?");
    $stmt->bind_param("ssdii", $status, $kerusakan_fix, $biaya, $id, $teknisi_id);

    if ($stmt->execute()) {
        $success_msg = "Data servis berhasil diupdate!";
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM servis WHERE id = ?");
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
    <title>Update Servis - FixTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="container mx-auto px-4 py-8 max-w-3xl">
        
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <a href="index.php" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-green-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Update Servis</h1>
                <p class="text-slate-500 text-sm">No. Resi: <span class="font-semibold text-green-600"><?php echo $servis['no_resi']; ?></span></p>
            </div>
        </div>

        <?php if ($success_msg): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
                <p class="font-medium"><?php echo $success_msg; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r" role="alert">
                <p><?php echo $error_msg; ?></p>
            </div>
        <?php endif; ?>

        <!-- Info Barang (Read Only) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
            <h3 class="text-lg font-bold text-slate-700 mb-4 border-b pb-2">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i> Informasi Barang
            </h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-slate-400">Pelanggan:</span>
                    <p class="font-medium text-slate-700"><?php echo $servis['nama_pelanggan']; ?> (<?php echo $servis['no_hp']; ?>)</p>
                </div>
                <div>
                    <span class="text-slate-400">Barang:</span>
                    <p class="font-medium text-slate-700"><?php echo $servis['nama_barang']; ?></p>
                </div>
                <div>
                    <span class="text-slate-400">Kelengkapan:</span>
                    <p class="font-medium text-slate-700"><?php echo $servis['kelengkapan']; ?></p>
                </div>
                <div>
                    <span class="text-slate-400">Estimasi:</span>
                    <p class="font-medium text-slate-700"><?php echo $servis['estimasi_hari'] ? $servis['estimasi_hari'] . ' Hari' : '-'; ?></p>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400">Keluhan Awal:</span>
                    <p class="font-medium text-slate-700 mt-1 bg-slate-50 p-3 rounded-lg"><?php echo $servis['keluhan_awal']; ?></p>
                </div>
            </div>
        </div>

        <!-- Form Update -->
        <form action="" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-bold text-slate-700 border-b pb-2">
                    <i class="fas fa-edit mr-2 text-green-500"></i> Update Status & Hasil
                </h3>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status Pengerjaan</label>
                    <select name="status" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="Pengecekan" <?php echo $servis['status'] == 'Pengecekan' ? 'selected' : ''; ?>>Pengecekan</option>
                        <option value="Menunggu Sparepart" <?php echo $servis['status'] == 'Menunggu Sparepart' ? 'selected' : ''; ?>>Menunggu Sparepart</option>
                        <option value="Pengerjaan" <?php echo $servis['status'] == 'Pengerjaan' ? 'selected' : ''; ?>>Pengerjaan</option>
                        <option value="Selesai" <?php echo $servis['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="Batal" <?php echo $servis['status'] == 'Batal' ? 'selected' : ''; ?>>Batal</option>
                    </select>
                </div>

                <!-- Kerusakan Fix -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kerusakan yang Diperbaiki</label>
                    <textarea name="kerusakan_fix" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 placeholder-slate-400" placeholder="Jelaskan kerusakan yang sudah diperbaiki..."><?php echo htmlspecialchars($servis['kerusakan_fix'] ?? ''); ?></textarea>
                </div>

                <!-- Biaya -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Biaya Servis (Rp)</label>
                    <input type="number" name="biaya" value="<?php echo $servis['biaya'] ?? ''; ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 placeholder-slate-400" placeholder="Contoh: 150000">
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end gap-4">
                <a href="index.php" class="px-6 py-3 rounded-xl font-medium text-slate-600 hover:bg-white transition-colors border border-slate-200">
                    Batal
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-green-200 transition-all">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>

</body>
</html>
