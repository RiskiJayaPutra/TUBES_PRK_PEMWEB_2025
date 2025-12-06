<?php
// ============================================
// CETAK STRUK - cetak_struk.php
// ============================================
// File ini menangani pembuatan dan pencetakan struk service

header('Content-Type: text/html; charset=utf-8');

// Ambil data dari request
$serviceId = isset($_GET['serviceId']) ? intval($_GET['serviceId']) : null;
$customerName = isset($_GET['customerName']) ? htmlspecialchars($_GET['customerName']) : '';
$itemName = isset($_GET['itemName']) ? htmlspecialchars($_GET['itemName']) : '';
$diagnosisDesc = isset($_GET['diagnosisDesc']) ? htmlspecialchars($_GET['diagnosisDesc']) : '';
$additionalDetails = isset($_GET['additionalDetails']) ? htmlspecialchars($_GET['additionalDetails']) : '';
$components = isset($_GET['components']) ? json_decode($_GET['components'], true) : [];
$laborCost = isset($_GET['laborCost']) ? floatval($_GET['laborCost']) : 0;

// Fungsi untuk format currency
function formatCurrency($value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
}

// Hitung total komponen
$componentTotal = 0;
foreach ($components as $comp) {
    $componentTotal += isset($comp['cost']) ? $comp['cost'] : 0;
}
$total = $componentTotal + $laborCost;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Layanan Perbaikan - FixTrack</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 12px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .diagnosis-section {
            margin-bottom: 20px;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 15px 0;
        }
        
        .diagnosis-section h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .diagnosis-text {
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin-bottom: 8px;
        }
        
        .components-section {
            margin-bottom: 20px;
        }
        
        .components-section h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 12px;
        }
        
        table thead {
            border-bottom: 1px solid #999;
        }
        
        table th {
            text-align: left;
            padding: 8px 0;
            font-weight: bold;
        }
        
        table td {
            padding: 6px 0;
            border-bottom: 1px solid #ddd;
        }
        
        table td:last-child {
            text-align: right;
        }
        
        .totals {
            margin-top: 15px;
            font-size: 13px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        
        .grand-total {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 14px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 10px;
            color: #999;
            font-style: italic;
            font-size: 12px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                max-width: 100%;
            }
            
            .print-button {
                display: none;
            }
        }
        
        .print-button {
            display: block;
            margin: 20px auto 0;
            padding: 10px 30px;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background-color: #0052a3;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>STRUK LAYANAN PERBAIKAN</h1>
            <p>FixTrack Service Center</p>
            <p>Tanggal: <?php echo date('d/m/Y H:i'); ?></p>
        </div>

        <!-- Info Pelanggan -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Pelanggan:</span>
                <span><?php echo $customerName ?: '(Tidak ada)'; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Barang:</span>
                <span><?php echo $itemName ?: '(Tidak ada)'; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">No. Servis:</span>
                <span>#<?php echo str_pad($serviceId, 3, '0', STR_PAD_LEFT); ?></span>
            </div>
        </div>

        <!-- Diagnosa -->
        <div class="diagnosis-section">
            <h3>Deskripsi Diagnosa:</h3>
            <div class="diagnosis-text">
                <?php echo $diagnosisDesc ?: 'Tidak ada deskripsi diagnosa'; ?>
            </div>
            <?php if ($additionalDetails): ?>
                <h3>Catatan Tambahan:</h3>
                <div class="diagnosis-text">
                    <?php echo $additionalDetails; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Komponen & Biaya -->
        <div class="components-section">
            <h3>Komponen & Biaya:</h3>
            
            <?php if (!empty($components) && count($components) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Komponen</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($components as $comp): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($comp['name'] ?? 'Komponen'); ?></td>
                                <td><?php echo formatCurrency($comp['cost'] ?? 0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">Tidak ada komponen</div>
            <?php endif; ?>
        </div>

        <!-- Ringkasan Biaya -->
        <div class="totals">
            <div class="total-row">
                <span>Total Komponen:</span>
                <span><?php echo formatCurrency($componentTotal); ?></span>
            </div>
            <div class="total-row">
                <span>Biaya Jasa:</span>
                <span><?php echo formatCurrency($laborCost); ?></span>
            </div>
            <div class="grand-total">
                <span>TOTAL KESELURUHAN:</span>
                <span><?php echo formatCurrency($total); ?></span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah menggunakan layanan kami</p>
            <p>FixTrack © <?php echo date('Y'); ?> - Semua Hak Dilindungi</p>
        </div>

        <!-- Print Button -->
        <button class="print-button" onclick="window.print()">Cetak Struk</button>
    </div>

    <script>
        // Auto print jika parameter autoprint=1
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === '1') {
            window.addEventListener('load', function() {
                window.print();
            });
        }
    </script>
</body>
</html>
