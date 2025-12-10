<?php
require "../koneksi.php";

$q = mysqli_query($conn, "SELECT * FROM servis ORDER BY tgl_masuk DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        th, td { border:1px solid #333; padding:8px; font-size:14px; }
        th { background:#ddd; }
    </style>
</head>

<body onload="window.print()">

<h2>Laporan Servis FixTrack</h2>
<p><?= date("d-m-Y H:i") ?></p>

<table>
    <thead>
        <tr>
            <th>No Resi</th>
            <th>Tgl Masuk</th>
            <th>Pelanggan</th>
            <th>Barang</th>
            <th>Status</th>
            <th>Biaya</th>
        </tr>
    </thead>

    <tbody>
        <?php while($r = mysqli_fetch_assoc($q)) { ?>
        <tr>
            <td><?= $r['no_resi'] ?></td>
            <td><?= $r['tgl_masuk'] ?></td>
            <td><?= $r['nama_pelanggan'] ?></td>
            <td><?= $r['nama_barang'] ?></td>
            <td><?= $r['status'] ?></td>
            <td><?= $r['biaya'] ? "Rp ".number_format($r['biaya'],0,',','.') : "-" ?></td>
        </tr>
        <?php } ?>
    </tbody>

</table>

</body>
</html>
