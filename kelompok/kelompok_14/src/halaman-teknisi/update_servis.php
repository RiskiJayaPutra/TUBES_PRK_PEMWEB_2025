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

// Ambil rincian biaya yang sudah ada
$biaya_items = $conn->query("SELECT * FROM biaya_item WHERE id_servis = $id ORDER BY id ASC");

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kerusakan_fix = $_POST['kerusakan_fix'];
    // Hapus titik dari format rupiah sebelum konversi ke angka
    $biaya_jasa = floatval(str_replace('.', '', $_POST['biaya_jasa'] ?? '0'));
    
    // Hapus semua biaya komponen lama
    $conn->query("DELETE FROM biaya_item WHERE id_servis = $id");
    
    // Simpan biaya jasa sebagai item
    if ($biaya_jasa > 0) {
        $conn->query("INSERT INTO biaya_item (id_servis, nama_item, harga) VALUES ($id, 'Biaya Jasa', $biaya_jasa)");
    }
    
    // Simpan komponen
    $total_biaya = $biaya_jasa;
    $nama_komponen = $_POST['nama_komponen'] ?? [];
    $harga_komponen = $_POST['harga_komponen'] ?? [];
    
    for ($i = 0; $i < count($nama_komponen); $i++) {
        if (!empty($nama_komponen[$i]) && !empty($harga_komponen[$i])) {
            $nama = $conn->real_escape_string($nama_komponen[$i]);
            // Hapus titik dari format rupiah
            $harga = floatval(str_replace('.', '', $harga_komponen[$i]));
            $conn->query("INSERT INTO biaya_item (id_servis, nama_item, harga) VALUES ($id, '$nama', $harga)");
            $total_biaya += $harga;
        }
    }

    $stmt = $conn->prepare("UPDATE servis SET kerusakan_fix = ?, biaya = ? WHERE id = ? AND id_teknisi = ?");
    $stmt->bind_param("sdii", $kerusakan_fix, $total_biaya, $id, $teknisi_id);

    if ($stmt->execute()) {
        $success_msg = "Data servis berhasil diupdate!";
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM servis WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $servis = $stmt->get_result()->fetch_assoc();
        // Refresh biaya items
        $biaya_items = $conn->query("SELECT * FROM biaya_item WHERE id_servis = $id ORDER BY id ASC");
    } else {
        $error_msg = "Gagal update: " . $conn->error;
    }
}

// Pisahkan biaya jasa dan komponen
$biaya_jasa = 0;
$komponen_list = [];
if ($biaya_items) {
    while ($item = $biaya_items->fetch_assoc()) {
        if ($item['nama_item'] == 'Biaya Jasa') {
            $biaya_jasa = $item['harga'];
        } else {
            $komponen_list[] = $item;
        }
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

    <div class="container mx-auto px-6 py-8 max-w-5xl">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="index.php" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-green-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Detail Servis</h1>
                    <p class="text-slate-500 text-sm">No. Resi: <span class="font-semibold text-green-600"><?php echo $servis['no_resi']; ?></span>
                        <span class="ml-2 px-2 py-1 text-xs rounded-full font-semibold
                        <?php 
                            if ($servis['status'] == 'Selesai') echo 'bg-green-100 text-green-700';
                            elseif ($servis['status'] == 'Pengerjaan') echo 'bg-yellow-100 text-yellow-700';
                            elseif ($servis['status'] == 'Pengecekan') echo 'bg-indigo-100 text-indigo-700';
                            elseif ($servis['status'] == 'Menunggu Sparepart') echo 'bg-orange-100 text-orange-700';
                            else echo 'bg-gray-100 text-gray-700';
                        ?>"><?php echo $servis['status']; ?></span>
                    </p>
                </div>
            </div>
            <a href="print_resi.php?id=<?php echo $id; ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
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

        <!-- Info Barang -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
            <h3 class="text-sm font-semibold text-slate-500 uppercase mb-4">Informasi Barang</h3>
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
                    <span class="text-slate-400">Estimasi</span>
                    <p class="font-medium text-slate-700"><?php echo $servis['estimasi_hari'] ? $servis['estimasi_hari'] . ' Hari' : '-'; ?></p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-slate-400 text-sm">Keluhan Awal:</span>
                <p class="font-medium text-slate-700 mt-1 bg-slate-50 p-3 rounded-lg text-sm"><?php echo $servis['keluhan_awal']; ?></p>
            </div>
            
            <!-- Timeline Tanggal (Otomatis) -->
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="bg-blue-50 p-3 rounded-lg text-center">
                        <div class="text-xs text-blue-600 mb-1">Tgl Diterima</div>
                        <div class="font-bold text-slate-800"><?php echo date('d M Y', strtotime($servis['tgl_masuk'])); ?></div>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded-lg text-center">
                        <div class="text-xs text-yellow-600 mb-1">Tgl Mulai</div>
                        <div class="font-bold text-slate-800"><?php echo $servis['tgl_mulai'] ? date('d M Y', strtotime($servis['tgl_mulai'])) : '-'; ?></div>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg text-center">
                        <div class="text-xs text-green-600 mb-1">Tgl Selesai</div>
                        <div class="font-bold text-slate-800"><?php echo $servis['tgl_selesai'] ? date('d M Y', strtotime($servis['tgl_selesai'])) : '-'; ?></div>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-2 text-center">* Tanggal otomatis terisi saat status berubah ke Pengerjaan/Selesai</p>
            </div>
        </div>

        <!-- Form Update -->
        <form action="" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-bold text-slate-700 border-b pb-2">
                    <i class="fas fa-edit mr-2 text-green-500"></i> Hasil Diagnosa & Rincian Biaya
                </h3>

                <!-- Hasil Diagnosa -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Hasil Diagnosa / Kerusakan yang Diperbaiki</label>
                    <textarea name="kerusakan_fix" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Jelaskan kerusakan yang ditemukan dan diperbaiki..."><?php echo htmlspecialchars($servis['kerusakan_fix'] ?? ''); ?></textarea>
                </div>

                <!-- Biaya Jasa -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <label class="block text-sm font-medium text-blue-700 mb-2">
                        <i class="fas fa-hand-holding-usd mr-1"></i> Biaya Jasa (Rp)
                    </label>
                    <input type="text" name="biaya_jasa" value="<?php echo $biaya_jasa ? number_format($biaya_jasa, 0, ',', '.') : ''; ?>" placeholder="Contoh: 100.000" 
                        class="w-full px-4 py-3 bg-white border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 biaya-input" oninput="formatRupiah(this); hitungTotal()">
                </div>

                <!-- Biaya Komponen -->
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-sm font-medium text-orange-700">
                            <i class="fas fa-microchip mr-1"></i> Biaya Komponen / Sparepart
                        </label>
                        <button type="button" onclick="addKomponen()" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i> Tambah Komponen
                        </button>
                    </div>
                    
                    <div id="komponen-container" class="space-y-3">
                        <?php if (count($komponen_list) > 0): ?>
                            <?php foreach ($komponen_list as $item): ?>
                                <div class="komponen-row flex gap-3 items-center">
                                    <input type="text" name="nama_komponen[]" value="<?php echo htmlspecialchars($item['nama_item']); ?>" placeholder="Nama komponen (cth: LCD Baru)" class="flex-1 px-4 py-3 bg-white border border-orange-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <input type="text" name="harga_komponen[]" value="<?php echo $item['harga'] ? number_format($item['harga'], 0, ',', '.') : ''; ?>" placeholder="Harga" class="w-40 px-4 py-3 bg-white border border-orange-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 biaya-input" oninput="formatRupiah(this); hitungTotal()">
                                    <button type="button" onclick="removeKomponen(this)" class="text-red-500 hover:text-red-600 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="komponen-row flex gap-3 items-center">
                                <input type="text" name="nama_komponen[]" placeholder="Nama komponen (cth: LCD Baru)" class="flex-1 px-4 py-3 bg-white border border-orange-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <input type="text" name="harga_komponen[]" placeholder="Harga" class="w-40 px-4 py-3 bg-white border border-orange-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 biaya-input" oninput="formatRupiah(this); hitungTotal()">
                                <button type="button" onclick="removeKomponen(this)" class="text-red-500 hover:text-red-600 p-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Total -->
                <div class="bg-green-100 p-4 rounded-lg border border-green-300 flex justify-between items-center">
                    <span class="font-semibold text-green-800">Total Biaya:</span>
                    <span id="total-biaya" class="text-2xl font-bold text-green-800">Rp <?php echo number_format($servis['biaya'] ?? 0, 0, ',', '.'); ?></span>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end gap-4">
                <a href="index.php" class="px-6 py-3 rounded-lg font-medium text-slate-600 hover:bg-white transition-colors border border-slate-200">
                    Batal
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg shadow-green-200 transition-all">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>

    <script>
        function addKomponen() {
            const container = document.getElementById('komponen-container');
            const row = document.createElement('div');
            row.className = 'komponen-row flex gap-3 items-center';
            row.innerHTML = `
                <input type="text" name="nama_komponen[]" placeholder="Nama komponen (cth: Baterai)" class="flex-1 px-4 py-3 bg-white border border-orange-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                <input type="text" name="harga_komponen[]" placeholder="Harga" class="w-40 px-4 py-3 bg-white border border-orange-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 biaya-input" oninput="formatRupiah(this); hitungTotal()">
                <button type="button" onclick="removeKomponen(this)" class="text-red-500 hover:text-red-600 p-2">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            container.appendChild(row);
        }

        function removeKomponen(btn) {
            const rows = document.querySelectorAll('.komponen-row');
            if (rows.length > 1) {
                btn.closest('.komponen-row').remove();
                hitungTotal();
            }
        }

        function formatRupiah(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value) {
                input.value = parseInt(value).toLocaleString('id-ID');
            }
        }

        function getRawValue(str) {
            return parseInt(str.replace(/[^0-9]/g, '')) || 0;
        }

        function hitungTotal() {
            let total = 0;
            document.querySelectorAll('.biaya-input').forEach(input => {
                total += getRawValue(input.value);
            });
            document.getElementById('total-biaya').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    </script>

</body>
</html>
