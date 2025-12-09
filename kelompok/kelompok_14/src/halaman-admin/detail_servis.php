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
$stmt = $conn->prepare("SELECT servis.*, users.nama as nama_teknisi 
                        FROM servis 
                        LEFT JOIN users ON servis.id_teknisi = users.id 
                        WHERE servis.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: list_servis.php");
    exit();
}

$servis = $result->fetch_assoc();

// Warna badge status
$status_colors = [
    'Barang Masuk' => 'bg-blue-100 text-blue-700',
    'Pengecekan' => 'bg-indigo-100 text-indigo-700',
    'Menunggu Sparepart' => 'bg-orange-100 text-orange-700',
    'Pengerjaan' => 'bg-yellow-100 text-yellow-700',
    'Selesai' => 'bg-green-100 text-green-700',
    'Diambil' => 'bg-gray-100 text-gray-700',
    'Batal' => 'bg-red-100 text-red-700'
];
$status_class = $status_colors[$servis['status']] ?? 'bg-slate-100 text-slate-700';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Servis - FixTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="container mx-auto px-4 py-8 max-w-3xl">
        
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <a href="list_servis.php" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-slate-800">Detail Servis</h1>
                <p class="text-slate-500 text-sm">No. Resi: <span class="font-bold text-blue-600"><?php echo $servis['no_resi']; ?></span></p>
            </div>
            <span class="<?php echo $status_class; ?> px-4 py-2 rounded-full text-sm font-semibold">
                <?php echo $servis['status']; ?>
            </span>
        </div>

        <!-- Detail Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
            
            <!-- Info Pelanggan -->
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-700 mb-4">
                    <i class="fas fa-user mr-2 text-blue-500"></i> Informasi Pelanggan
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wide">Nama</span>
                        <p class="font-semibold text-slate-700"><?php echo htmlspecialchars($servis['nama_pelanggan']); ?></p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wide">No. HP</span>
                        <p class="font-semibold text-slate-700">
                            <a href="https://wa.me/<?php echo preg_replace('/^0/', '62', $servis['no_hp']); ?>" target="_blank" class="text-green-600 hover:underline">
                                <?php echo $servis['no_hp']; ?> <i class="fab fa-whatsapp ml-1"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Info Barang -->
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-700 mb-4">
                    <i class="fas fa-box mr-2 text-blue-500"></i> Informasi Barang
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wide">Nama Barang</span>
                        <p class="font-semibold text-slate-700"><?php echo htmlspecialchars($servis['nama_barang']); ?></p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wide">Kelengkapan</span>
                        <p class="font-semibold text-slate-700"><?php echo htmlspecialchars($servis['kelengkapan']); ?></p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-xs text-slate-400 uppercase tracking-wide">Keluhan Awal</span>
                        <p class="font-medium text-slate-600 mt-1 bg-slate-50 p-3 rounded-lg"><?php echo nl2br(htmlspecialchars($servis['keluhan_awal'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Info Teknis -->
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-700 mb-4">
                    <i class="fas fa-wrench mr-2 text-blue-500"></i> Informasi Teknis
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wide">Tanggal Masuk</span>
                        <p class="font-semibold text-slate-700"><?php echo date('d M Y, H:i', strtotime($servis['tgl_masuk'])); ?></p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wide">Estimasi</span>
                        <p class="font-semibold text-slate-700"><?php echo $servis['estimasi_hari'] ? $servis['estimasi_hari'] . ' Hari' : '-'; ?></p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wide">Teknisi</span>
                        <p class="font-semibold text-slate-700"><?php echo $servis['nama_teknisi'] ?? '-'; ?></p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wide">Tanggal Keluar</span>
                        <p class="font-semibold text-slate-700"><?php echo $servis['tgl_keluar'] ? date('d M Y, H:i', strtotime($servis['tgl_keluar'])) : '-'; ?></p>
                    </div>
                    <?php if ($servis['kerusakan_fix']): ?>
                    <div class="col-span-2">
                        <span class="text-xs text-slate-400 uppercase tracking-wide">Kerusakan yang Diperbaiki</span>
                        <p class="font-medium text-slate-600 mt-1 bg-green-50 p-3 rounded-lg"><?php echo nl2br(htmlspecialchars($servis['kerusakan_fix'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Biaya -->
            <div class="p-6 bg-slate-50">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 font-medium">Total Biaya:</span>
                    <span class="text-2xl font-bold text-blue-600">
                        <?php echo $servis['biaya'] ? 'Rp ' . number_format($servis['biaya'], 0, ',', '.') : '-'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between">
            <a href="list_servis.php" class="px-6 py-3 rounded-xl font-medium text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <?php if ($servis['status'] === 'Selesai'): ?>
                <a href="update_status.php?id=<?php echo $servis['id']; ?>&status=Diambil" onclick="return confirm('Konfirmasi barang sudah diambil?')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-bold transition-colors">
                    <i class="fas fa-check-double mr-2"></i> Tandai Diambil
                </a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
