<?php
require_once '../config.php';

$resi = $_GET['resi'] ?? '';
$servis = null;
$error = '';

if ($resi) {
    $stmt = $conn->prepare("SELECT servis.*, users.nama as nama_teknisi 
                            FROM servis 
                            LEFT JOIN users ON servis.id_teknisi = users.id 
                            WHERE servis.no_resi = ?");
    $stmt->bind_param("s", $resi);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $servis = $result->fetch_assoc();
    } else {
        $error = 'Nomor resi tidak ditemukan. Periksa kembali nomor resi Anda.';
    }
}

$status_colors = [
    'Barang Masuk' => 'bg-blue-500',
    'Pengecekan' => 'bg-indigo-500',
    'Menunggu Sparepart' => 'bg-orange-500',
    'Pengerjaan' => 'bg-yellow-500',
    'Selesai' => 'bg-green-500',
    'Diambil' => 'bg-gray-500',
    'Batal' => 'bg-red-500'
];
$status_color = $status_colors[$servis['status'] ?? ''] ?? 'bg-gray-500';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Servis - FixTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">

    <!-- Navbar Sticky -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <img src="../assets/photos/logo.png" alt="FixTrack" class="h-16 w-16 object-contain">
                <span class="font-bold text-lg text-slate-800">FixTrack</span>
            </div>
            <a href="../login.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg font-medium">
                Login
            </a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-10">
        
        <!-- Judul -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Cek Status Servis</h1>
            <p class="text-slate-500">Masukkan nomor resi untuk melacak barang Anda</p>
        </div>

        <!-- Form Cek Resi -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <form action="" method="GET" class="flex gap-3">
                <input type="text" name="resi" value="<?php echo htmlspecialchars($resi); ?>" 
                    placeholder="Contoh: SRV-202512091156" 
                    class="flex-1 px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500"
                    required>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                    <i class="fas fa-search mr-2"></i> Cek
                </button>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($servis): ?>
            <!-- Hasil Pencarian -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                
                <!-- Header Status -->
                <div class="<?php echo $status_color; ?> text-white px-6 py-5">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-sm opacity-80">Status Servis</div>
                            <div class="text-2xl font-bold"><?php echo $servis['status']; ?></div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm opacity-80">No. Resi</div>
                            <div class="text-lg font-bold"><?php echo $servis['no_resi']; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Detail Servis -->
                <div class="p-6">
                    
                    <!-- Timeline Tanggal -->
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-500 uppercase mb-4">Riwayat Tanggal</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <div class="text-xs text-blue-600 mb-1">Tanggal Diterima</div>
                                <div class="font-bold text-slate-800"><?php echo $servis['tgl_masuk'] ? date('d M Y', strtotime($servis['tgl_masuk'])) : '-'; ?></div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                                <div class="text-xs text-yellow-600 mb-1">Mulai Dikerjakan</div>
                                <div class="font-bold text-slate-800"><?php echo $servis['tgl_mulai'] ? date('d M Y', strtotime($servis['tgl_mulai'])) : '-'; ?></div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <div class="text-xs text-green-600 mb-1">Selesai Dikerjakan</div>
                                <div class="font-bold text-slate-800"><?php echo $servis['tgl_selesai'] ? date('d M Y', strtotime($servis['tgl_selesai'])) : '-'; ?></div>
                            </div>
                            <div class="bg-gray-100 p-4 rounded-lg text-center">
                                <div class="text-xs text-gray-600 mb-1">Diambil Pemilik</div>
                                <div class="font-bold text-slate-800"><?php echo $servis['tgl_keluar'] ? date('d M Y', strtotime($servis['tgl_keluar'])) : '-'; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Kolom Kiri -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-500 uppercase mb-4">Informasi Pelanggan</h3>
                            <table class="w-full text-sm">
                                <tr>
                                    <td class="py-2 text-slate-500 w-40">Nama Pelanggan</td>
                                    <td class="py-2 text-slate-800 font-medium"><?php echo htmlspecialchars($servis['nama_pelanggan']); ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-slate-500">No. HP</td>
                                    <td class="py-2 text-slate-800"><?php echo $servis['no_hp']; ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Kolom Kanan -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-500 uppercase mb-4">Informasi Barang</h3>
                            <table class="w-full text-sm">
                                <tr>
                                    <td class="py-2 text-slate-500 w-40">Nama Barang</td>
                                    <td class="py-2 text-slate-800 font-medium"><?php echo htmlspecialchars($servis['nama_barang']); ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-slate-500">Kelengkapan</td>
                                    <td class="py-2 text-slate-800"><?php echo htmlspecialchars($servis['kelengkapan']); ?></td>
                                </tr>
                                <?php if ($servis['estimasi_hari']): ?>
                                <tr>
                                    <td class="py-2 text-slate-500">Estimasi</td>
                                    <td class="py-2 text-slate-800"><?php echo $servis['estimasi_hari']; ?> Hari</td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($servis['biaya']): ?>
                                <tr>
                                    <td class="py-2 text-slate-500">Total Biaya</td>
                                    <td class="py-2 text-green-600 font-bold">Rp <?php echo number_format($servis['biaya'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <!-- Keluhan -->
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h3 class="text-sm font-semibold text-slate-500 uppercase mb-3">Keluhan Awal</h3>
                        <div class="bg-slate-50 p-4 rounded-lg text-sm text-slate-700">
                            <?php echo nl2br(htmlspecialchars($servis['keluhan_awal'])); ?>
                        </div>
                    </div>

                    <?php if ($servis['kerusakan_fix']): ?>
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-slate-500 uppercase mb-3">Hasil Perbaikan</h3>
                        <div class="bg-green-50 p-4 rounded-lg text-sm text-green-700">
                            <?php echo nl2br(htmlspecialchars($servis['kerusakan_fix'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($servis['status'] === 'Selesai'): ?>
                    <div class="mt-6 bg-green-100 border border-green-300 rounded-lg p-5 text-center">
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                        <div class="font-bold text-green-700 text-lg">Barang Siap Diambil!</div>
                        <div class="text-sm text-green-600 mt-1">Silakan datang ke bengkel dengan membawa bukti resi ini.</div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Footer -->
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-between items-center text-sm">
                    <span class="text-slate-500">Butuh bantuan?</span>
                    <a href="https://wa.me/6281234567890" class="text-green-600 font-medium hover:underline">
                        <i class="fab fa-whatsapp mr-1"></i> Hubungi WhatsApp
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>
