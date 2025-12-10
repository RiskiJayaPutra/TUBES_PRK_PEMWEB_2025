<?php
session_start();
require_once '../config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data servis
$stmt = $conn->prepare("SELECT s.*, u.nama as nama_teknisi FROM servis s LEFT JOIN users u ON s.id_teknisi = u.id WHERE s.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data tidak ditemukan");
}

$servis = $result->fetch_assoc();

// Ambil rincian biaya
$biaya_items = $conn->query("SELECT * FROM biaya_item WHERE id_servis = $id ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Resi - <?php echo $servis['no_resi']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        .container {
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            font-size: 10px;
            color: #666;
        }
        .resi-number {
            text-align: center;
            background: #f0f0f0;
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .resi-number .label {
            font-size: 10px;
            color: #666;
        }
        .resi-number .number {
            font-size: 16px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 10px;
        }
        .section-title {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }
        .row .label {
            color: #666;
        }
        .row .value {
            font-weight: 500;
            text-align: right;
        }
        .biaya-item {
            padding: 3px 0;
            display: flex;
            justify-content: space-between;
        }
        .total {
            border-top: 2px solid #333;
            padding-top: 5px;
            margin-top: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        .status {
            text-align: center;
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-selesai { background: #d4edda; color: #155724; }
        .status-pengerjaan { background: #fff3cd; color: #856404; }
        .status-pengecekan { background: #e2e3f1; color: #383d6e; }
        .status-menunggu { background: #ffe5d0; color: #8a4500; }
        .footer {
            text-align: center;
            border-top: 2px dashed #333;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 10px;
            color: #666;
        }
        .print-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 15px;
        }
        .print-btn:hover {
            background: #218838;
        }
        @media print {
            .print-btn { display: none; }
            body { background: white; }
            .container { max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>FixTrack</h1>
            <p>Layanan Servis Elektronik</p>
        </div>

        <!-- Resi Number -->
        <div class="resi-number">
            <div class="label">NO. RESI</div>
            <div class="number"><?php echo $servis['no_resi']; ?></div>
        </div>

        <!-- Status -->
        <div class="status 
            <?php 
                if ($servis['status'] == 'Selesai') echo 'status-selesai';
                elseif ($servis['status'] == 'Pengerjaan') echo 'status-pengerjaan';
                elseif ($servis['status'] == 'Pengecekan') echo 'status-pengecekan';
                elseif ($servis['status'] == 'Menunggu Sparepart') echo 'status-menunggu';
            ?>">
            Status: <?php echo $servis['status']; ?>
        </div>

        <!-- Pelanggan -->
        <div class="section">
            <div class="section-title">Data Pelanggan</div>
            <div class="row">
                <span class="label">Nama</span>
                <span class="value"><?php echo $servis['nama_pelanggan']; ?></span>
            </div>
            <div class="row">
                <span class="label">No. HP</span>
                <span class="value"><?php echo $servis['no_hp']; ?></span>
            </div>
        </div>

        <!-- Barang -->
        <div class="section">
            <div class="section-title">Data Barang</div>
            <div class="row">
                <span class="label">Barang</span>
                <span class="value"><?php echo $servis['nama_barang']; ?></span>
            </div>
            <div class="row">
                <span class="label">Keluhan</span>
                <span class="value" style="max-width: 60%;"><?php echo $servis['keluhan_awal']; ?></span>
            </div>
            <?php if ($servis['kerusakan_fix']): ?>
            <div class="row">
                <span class="label">Diagnosa</span>
                <span class="value" style="max-width: 60%;"><?php echo $servis['kerusakan_fix']; ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tanggal -->
        <div class="section">
            <div class="section-title">Timeline</div>
            <div class="row">
                <span class="label">Tgl Masuk</span>
                <span class="value"><?php echo date('d/m/Y H:i', strtotime($servis['tgl_masuk'])); ?></span>
            </div>
            <?php if ($servis['tgl_mulai']): ?>
            <div class="row">
                <span class="label">Tgl Mulai</span>
                <span class="value"><?php echo date('d/m/Y H:i', strtotime($servis['tgl_mulai'])); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($servis['tgl_selesai']): ?>
            <div class="row">
                <span class="label">Tgl Selesai</span>
                <span class="value"><?php echo date('d/m/Y H:i', strtotime($servis['tgl_selesai'])); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Rincian Biaya -->
        <?php if ($biaya_items && $biaya_items->num_rows > 0): ?>
        <div class="section">
            <div class="section-title">Rincian Biaya</div>
            <?php while ($item = $biaya_items->fetch_assoc()): ?>
            <div class="biaya-item">
                <span><?php echo $item['nama_item']; ?></span>
                <span>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></span>
            </div>
            <?php endwhile; ?>
            <div class="total row">
                <span>TOTAL</span>
                <span>Rp <?php echo number_format($servis['biaya'] ?? 0, 0, ',', '.'); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah menggunakan layanan kami</p>
            <p>Simpan resi ini untuk pengambilan barang</p>
            <p style="margin-top: 5px;"><?php echo date('d/m/Y H:i'); ?></p>
        </div>

        <button class="print-btn" onclick="window.print()">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>
</body>
</html>
