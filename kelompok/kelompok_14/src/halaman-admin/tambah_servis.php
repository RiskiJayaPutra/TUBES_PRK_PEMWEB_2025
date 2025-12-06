<?php
require_once '../config.php';

// Ambil data teknisi untuk dropdown
$teknisi_query = "SELECT id, nama FROM users WHERE role = 'teknisi'";
$teknisi_result = $conn->query($teknisi_query);

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate No Resi (Contoh sederhana: SRV-Timestamp)
    $no_resi = "SRV-" . date("YmdHi"); 
    
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_hp = $_POST['no_hp'];
    $nama_barang = $_POST['nama_barang'];
    $kelengkapan = $_POST['kelengkapan'];
    $keluhan_awal = $_POST['keluhan_awal'];
    $estimasi_hari = $_POST['estimasi_hari'];
    $id_teknisi = $_POST['id_teknisi']; // Bisa NULL jika belum dipilih

    // Query Insert
    $stmt = $conn->prepare("INSERT INTO servis (no_resi, nama_pelanggan, no_hp, nama_barang, kelengkapan, keluhan_awal, estimasi_hari, id_teknisi, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Barang Masuk')");
    
    // Bind parameters (s=string, i=int)
    // id_teknisi bisa kosong, perlu handling khusus jika kosong (NULL)
    if (empty($id_teknisi)) {
        $id_teknisi = NULL;
    }
    
    $stmt->bind_param("ssssssis", $no_resi, $nama_pelanggan, $no_hp, $nama_barang, $kelengkapan, $keluhan_awal, $estimasi_hari, $id_teknisi);

    if ($stmt->execute()) {
        $success_msg = "Data berhasil disimpan! No. Resi: <strong>$no_resi</strong>";
    } else {
        $error_msg = "Gagal menyimpan data: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Servis Baru - FixTrack</title>
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <a href="index.php" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Input Servis Baru</h1>
                <p class="text-slate-500 text-sm">Masukkan data barang masuk dari pelanggan</p>
            </div>
        </div>

        <?php if (isset($success_msg)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
                <p><?php echo $success_msg; ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r" role="alert">
                <p><?php echo $error_msg; ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Kolom Kiri: Data Pelanggan & Barang -->
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-slate-700 border-b pb-2 mb-4">
                        <i class="fas fa-user-circle mr-2 text-blue-500"></i> Data Pelanggan & Barang
                    </h3>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all placeholder-slate-400" placeholder="Contoh: Budi Santoso">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nomor HP / WhatsApp</label>
                        <input type="text" name="no_hp" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all placeholder-slate-400" placeholder="Contoh: 08123456789">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Barang (Jenis & Merek)</label>
                        <input type="text" name="nama_barang" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all placeholder-slate-400" placeholder="Contoh: Laptop Asus ROG / HP Samsung S20">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Kelengkapan</label>
                        <input type="text" name="kelengkapan" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all placeholder-slate-400" placeholder="Contoh: Unit + Charger + Tas">
                    </div>
                </div>

                <!-- Kolom Kanan: Kerusakan & Teknis -->
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-slate-700 border-b pb-2 mb-4">
                        <i class="fas fa-tools mr-2 text-blue-500"></i> Detail Kerusakan
                    </h3>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Kendala Awal / Keluhan</label>
                        <textarea name="keluhan_awal" required rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all placeholder-slate-400" placeholder="Jelaskan kerusakan yang dialami..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Estimasi (Hari)</label>
                            <div class="relative">
                                <input type="number" name="estimasi_hari" min="1" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all placeholder-slate-400" placeholder="3">
                                <span class="absolute right-4 top-3 text-slate-400 text-sm">Hari</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tugaskan Teknisi</label>
                            <select name="id_teknisi" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                                <option value="">-- Pilih Teknisi --</option>
                                <?php 
                                if ($teknisi_result->num_rows > 0) {
                                    while($row = $teknisi_result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['nama'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="bg-slate-50 px-8 py-5 border-t border-slate-100 flex justify-end gap-4">
                <button type="reset" class="px-6 py-3 rounded-xl font-medium text-slate-600 hover:bg-white hover:text-slate-800 transition-colors border border-transparent hover:border-slate-200">
                    Reset
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i> Simpan Data
                </button>
            </div>

        </form>
    </div>

</body>
</html>
