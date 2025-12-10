<?php
require "../koneksi.php";

// PENDAPATAN
$q_pendapatan = mysqli_query($conn, "SELECT SUM(biaya) AS total FROM servis");
$pendapatan = mysqli_fetch_assoc($q_pendapatan)['total'] ?? 0;

// RATA RATA SERVICE / BULAN
$q_rata = mysqli_query($conn, "
    SELECT AVG(jumlah) AS rata FROM (
        SELECT COUNT(*) AS jumlah FROM servis 
        GROUP BY YEAR(tgl_masuk), MONTH(tgl_masuk)
    ) AS t
");
$rata = mysqli_fetch_assoc($q_rata)['rata'] ?? 0;

// DATA SERVIS
$q_servis = mysqli_query($conn, "SELECT * FROM servis ORDER BY tgl_masuk DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Superadmin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6 min-h-screen">

<div class="max-w-5xl mx-auto">

    <h1 class="text-3xl font-bold text-[#001F3F] mb-3">Dashboard Superadmin</h1>
    <p class="text-gray-500 mb-6">Overview performa FixTrack</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white p-6 rounded-2xl shadow">
            <h2 class="text-gray-500">Total Pendapatan</h2>
            <p class="text-3xl text-green-600 font-bold mt-2">
                Rp <?= number_format($pendapatan,0,',','.') ?>
            </p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
            <h2 class="text-gray-500">Rata Service / Bulan</h2>
            <p class="text-3xl text-blue-600 font-bold mt-2">
                <?= number_format($rata,1) ?> unit
            </p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
            <h2 class="text-gray-500">Download Laporan</h2>
            <a href="laporan_print.php" class="mt-3 block text-center bg-[#001F3F] text-white py-2 rounded-lg hover:bg-[#00306B]">
                Cetak Laporan
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <h2 class="text-xl font-semibold mb-4 text-[#001F3F]">Laporan Servis</h2>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3">No Resi</th>
                    <th class="p-3">Tanggal Masuk</th>
                    <th class="p-3">Nama Pelanggan</th>
                    <th class="p-3">Barang</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Biaya</th>
                </tr>
            </thead>

            <tbody>
                <?php while($r = mysqli_fetch_assoc($q_servis)) { ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3"><?= $r['no_resi'] ?></td>
                    <td class="p-3"><?= $r['tgl_masuk'] ?></td>
                    <td class="p-3"><?= $r['nama_pelanggan'] ?></td>
                    <td class="p-3"><?= $r['nama_barang'] ?></td>
                    <td class="p-3"><?= $r['status'] ?></td>
                    <td class="p-3"><?= $r['biaya'] ? "Rp ".number_format($r['biaya'],0,',','.') : "-" ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>

</body>
</html>
