<?php
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $resi = mysqli_real_escape_string($koneksi, $_POST['resi']);

    $query = "SELECT no_resi FROM servis WHERE no_resi = '$resi' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        header("Location: tracking_resi.php?resi=" . urlencode($resi));
        exit;
    } else {
        $error = "Kode resi <b>$resi</b> tidak ditemukan dalam sistem.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Resi – FixTrack</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        <h1 class="text-3xl font-bold text-[#001F3F] text-center mb-2">
            FixTrack
        </h1>

        <p class="text-gray-600 text-center mb-6 text-sm">
            Cek status perbaikan dengan memasukkan Kode Resi Anda.
        </p>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded-lg mb-4 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">

            <div>
                <label class="block text-[#001F3F] font-medium mb-1">Kode Resi</label>
                <input 
                    type="text" 
                    name="resi"
                    required
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 
                           focus:ring-2 focus:ring-[#001F3F] focus:outline-none"
                    placeholder="Contoh: SRV-2023001"
                >
            </div>

            <button 
                type="submit"
                class="w-full bg-[#001F3F] hover:bg-[#002a55] text-white py-2 rounded-lg font-semibold transition">
                Cek Status
            </button>

        </form>

        <p class="text-center text-gray-500 text-xs mt-6">
            FixTrack — Service Tracking System
        </p>

    </div>

</body>
</html>
