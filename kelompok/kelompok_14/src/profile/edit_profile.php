<?php
session_start();
include '../config.php'; 

// --- 2. CEK LOGIN ---
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$pesan_error = "";

// Proses UPDATE
if (isset($_POST['update'])) {
    $nama_baru = trim($_POST['nama']);
    $username_baru = trim($_POST['username']);

    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $password_konfirmasi = $_POST['password_konfirmasi'];

    // Cek username sudah dipakai
    $cek_username = mysqli_query($conn, "SELECT * FROM users WHERE username='$username_baru' AND id != '$id_user'");
    if (mysqli_num_rows($cek_username) > 0) {
        $pesan_error = "Username sudah digunakan!";
    } else {

        // Apabila ingin ganti password
        if (!empty($password_lama) || !empty($password_baru) || !empty($password_konfirmasi)) {

            if (empty($password_lama) || empty($password_baru) || empty($password_konfirmasi)) {
                $pesan_error = "Semua kolom kata sandi harus diisi.";
            } else {
                // Ambil password lama dari DB
                $qPass = mysqli_query($conn, "SELECT password FROM users WHERE id='$id_user'");
                $dataPass = mysqli_fetch_assoc($qPass);
                $hashLama = $dataPass['password'];

                // Verifikasi password lama
                if (!password_verify($password_lama, $hashLama)) {
                    $pesan_error = "Kata sandi saat ini tidak sesuai!";
                } elseif ($password_baru != $password_konfirmasi) {
                    $pesan_error = "Kata sandi baru dan konfirmasi tidak sama!";
                } else {
                    // Hash password baru
                    $pass_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                    mysqli_query($conn, "UPDATE users SET password='$pass_hash' WHERE id='$id_user'");
                }
            }
        }

        // Jika tidak ada error → update nama dan username
        if (empty($pesan_error)) {
            mysqli_query($conn, "UPDATE users SET nama='$nama_baru', username='$username_baru' WHERE id='$id_user'");
            $_SESSION['nama'] = $nama_baru;

            echo "<script>
                alert('Data berhasil diperbarui!');
                window.location='profile.php';
            </script>";
        }
    }
}

// AMBIL DATA
$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$id_user'");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profil - FixTrack</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    theme: {
        extend: {
            fontFamily: { sans: ['Inter', 'sans-serif'] },
            colors: { primary:'#1e3a8a', accent:'#3b82f6', primaryHover:'#172554' },
        }
    }
}
</script>
</head>

<body class="bg-slate-50 min-h-screen font-sans">

<nav class="bg-primary text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <a href="../dashboard.php" class="flex items-center gap-2 hover:opacity-90 transition">
                <i class="fas fa-tools text-accent text-xl"></i>
                <span class="font-bold text-xl tracking-tight">
                    FixTrack <span class="font-normal text-blue-200">Edit Profil</span>
                </span>
            </a>

        </div>
    </div>
</nav>

<div class="max-w-3xl mx-auto px-4 py-12">

    <?php if ($pesan_error): ?>
        <div class="bg-red-500 text-white p-4 rounded-xl shadow-md mb-6 flex justify-between items-center">
            <span><i class="fas fa-exclamation-circle mr-2"></i><?= $pesan_error ?></span>
            <button onclick="this.parentElement.remove()" class="text-xl">&times;</button>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-2xl border overflow-hidden">

        <!-- HEADER -->
        <div class="h-32 bg-gradient-to-r from-primary to-blue-600 relative flex items-center justify-center text-white">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="relative text-center">
                <div class="text-5xl mb-2"><i class="fas fa-user-edit"></i></div>
                <h1 class="text-2xl font-bold">Edit Data Profil</h1>
            </div>
        </div>

        <form method="POST" class="p-10 space-y-10" id="formEditProfile">

            <div class="space-y-6">
                 <h2 class="text-lg font-bold text-primary">Informasi Akun</h2>

        <div>
            <label class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="<?= $data['nama'] ?>" required
                   class="mt-2 w-full p-3 rounded-xl bg-slate-50 border border-slate-200 shadow-sm">
        </div>

        <div>
            <label class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Username Login</label>
            <div class="mt-2 flex items-center bg-slate-50 border border-slate-200 rounded-xl shadow-sm p-3">
                <i class="fas fa-at text-slate-400 mr-3 text-xl"></i>
                <input id="username" name="username" value="<?= $data['username'] ?>" class="w-full bg-transparent outline-none font-medium">
            </div>
         </div>
       </div>

            <hr class="border-slate-200">

            <div class="space-y-6">
                <h2 class="text-lg font-bold text-red-600">Ganti Kata Sandi</h2>
                <p class="text-sm text-slate-500">Masukkan kata sandi saat ini untuk mengganti kata sandi. Jika tidak ingin mengubah, biarkan semua kolom kosong.</p>

                <div class="relative">
                    <label class="text-xs font-semibold text-red-600 uppercase tracking-widest">Kata Sandi Saat Ini</label>
                    <input type="password" id="password_lama" name="password_lama"
                           placeholder="Masukkan kata sandi saat ini"
                           class="mt-2 w-full p-3 pr-14 rounded-xl bg-slate-50 border border-slate-200 shadow-sm">
                    <i class="fas fa-eye toggle-password absolute right-5 top-1/2 mt-5 -translate-y-1/2 cursor-pointer text-slate-400"
                       data-target="password_lama"></i>
                </div>

                <div class="relative">
                    <label class="text-xs font-semibold text-green-600 uppercase tracking-widest">Kata Sandi Baru</label>
                    <input type="password" id="password_baru" name="password_baru"
                           placeholder="Masukkan kata sandi baru"
                           class="mt-2 w-full p-3 pr-14 rounded-xl bg-slate-50 border border-slate-200 shadow-sm">
                    <i class="fas fa-eye toggle-password absolute right-5 top-1/2 mt-5 -translate-y-1/2 cursor-pointer text-slate-400"
                       data-target="password_baru"></i>
                </div>

                <div class="relative">
                     <label class="text-xs font-semibold text-green-600 uppercase tracking-widest">Ulangi Kata Sandi Baru</label>
                     <input type="password" id="password_konfirmasi" name="password_konfirmasi"
                            placeholder="Tulis ulang kata sandi baru"
                            class="mt-2 w-full p-3 pr-14 rounded-xl bg-slate-50 border border-slate-200 shadow-sm">
                    <i class="fas fa-eye toggle-password absolute right-5 top-1/2 mt-5 -translate-y-1/2 cursor-pointer text-slate-400"
                       data-target="password_konfirmasi"></i>
                </div>
            </div>

            <button name="update"
                class="w-full bg-primary hover:bg-primaryHover text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>

            <div class="mt-4 pt-6 border-t border-slate-100 flex justify-end px-10 pb-10">
                <a href="profile.php" class="text-slate-500 hover:text-primary font-medium flex items-center gap-2 transition group">
                   <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Kembali ke Profil
                </a>
            </div>

    </div>
</div>

<script src="../assets/js/edit_profile.js"></script>

</body>
</html>