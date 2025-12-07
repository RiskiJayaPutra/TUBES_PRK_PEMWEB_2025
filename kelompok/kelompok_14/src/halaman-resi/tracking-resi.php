<?php
include "koneksi.php";

if (!isset($_GET['resi'])) {
    die("Resi tidak dikirim!");
}

$resi = mysqli_real_escape_string($koneksi, $_GET['resi']);

$query = "SELECT * FROM servis WHERE no_resi = '$resi' LIMIT 1";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<div style='padding:20px; font-family:Arial'>
            <h2>❌ Resi tidak ditemukan</h2>
            <p>Kode resi <b>$resi</b> tidak ada dalam sistem.</p>
            <a href='cek_resi.php'>Kembali</a>
          </div>";
    exit;
}

$data = mysqli_fetch_assoc($result);

// STATUS MAP utk progress bar
$status_list = [
    "Barang Masuk",
    "Pengecekan",
    "Menunggu Sparepart",
    "Pengerjaan",
    "Selesai",
    "Diambil"
];

$current_index = array_search($data['status'], $status_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Perbaikan – FixTrack</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10 px-4">

    <div class="w-full max-w-xl bg-white rounded-3xl shadow-xl p-8">

        <h1 class="text-3xl font-bold text-[#001F3F] text-center mb-2 tracking-wide">
            FixTrack
        </h1>
        <p class="text-gray-500 text-center text-sm mb-8">
            Status Perbaikan Perangkat Anda
        </p>

        <!-- KODE RESI -->
        <div class="text-center mb-8">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Kode Resi</p>
            <p class="text-2xl font-semibold text-[#001F3F] mt-1"><?= $data['no_resi'] ?></p>
        </div>

        <!-- PROGRESS BAR -->
        <div class="flex justify-between mb-10">
            <?php foreach ($status_list as $i => $s): ?>
                <div class="flex flex-col items-center">
                    <div class="h-4 w-4 
                        <?= $i <= $current_index ? 'bg-[#001F3F]' : 'bg-gray-300' ?> 
                        rounded-full"></div>
                    <p class="text-xs text-gray-600 mt-2"><?= $s ?></p>
                </div>

                <?php if ($i < count($status_list)-1): ?>
                    <div class="flex-1 
                        <?= $i < $current_index ? 'border-[#001F3F]' : 'border-gray-300' ?>
                        border-t-2 mx-2"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- DETAIL BARANG -->
        <div class="space-y-4 mb-10">
            <div>
                <p class="text-sm text-gray-500">Nama Barang</p>
                <p class="font-medium text-gray-800"><?= $data['nama_barang'] ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Keluhan</p>
                <p class="font-medium text-gray-800"><?= $data['keluhan_awal'] ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Tanggal Masuk</p>
                <p class="font-medium text-gray-800"><?= $data['tgl_masuk'] ?></p>
            </div>
        </div>

        <!-- STATUS SAAT INI -->
        <div class="bg-gray-50 border border-gray-200 p-5 rounded-2xl mb-10">
            <p class="uppercase text-xs text-gray-500 mb-1">Status Saat Ini</p>
            <p class="text-lg font-semibold text-[#001F3F] mb-2"><?= $data['status'] ?></p>

            <p class="text-sm text-gray-600 leading-relaxed">
                <?php if ($data['status'] == 'Barang Masuk'): ?>
                    Perangkat Anda baru saja diterima dan menunggu proses pengecekan awal.
                <?php elseif ($data['status'] == 'Pengecekan'): ?>
                    Teknisi sedang melakukan pengecekan komponen perangkat.
                <?php elseif ($data['status'] == 'Menunggu Sparepart'): ?>
                    Perangkat membutuhkan sparepart tambahan dan sedang dalam proses pengadaan.
                <?php elseif ($data['status'] == 'Pengerjaan'): ?>
                    Teknisi sedang mengerjakan perbaikan perangkat Anda.
                <?php elseif ($data['status'] == 'Selesai'): ?>
                    Perbaikan selesai dan perangkat siap diambil.
                <?php elseif ($data['status'] == 'Diambil'): ?>
                    Perangkat sudah diambil oleh pelanggan.
                <?php endif; ?>
            </p>
        </div>

        <a 
            href="cek_resi.php"
            class="block text-center bg-[#001F3F] hover:bg-[#002a55] 
                   text-white py-2.5 rounded-xl font-semibold transition shadow-md">
            Kembali
        </a>

    </div>

</body>
</html>
